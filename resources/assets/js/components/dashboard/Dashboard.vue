<template lang="html">
    <div class="dashboard" data-example-id="table-within-panel">
        <success :in_progress="in_progress" :success_messages="success_messages"></success>
        <div v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
            {{ status.message }}
        </div>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Dashboard
            <code v-if="board_name !== null" >{{ board_name }} </code>
                <a @click="synchronize()" >
                    <span class="glyphicon glyphicon-refresh pull-right" data-toggle="tooltip" data-placement="top"
                    :title="'Synchronize from ' + board_name" aria-hidden="true"></span>
                </a>
            </div>
            <div class="empty" v-show="loading_pins">
                <img src="/img/loader.gif" />
            </div>
            <!-- Table -->
            <table class="table" v-if="pins.length > 0">
                <thead>
                <tr>
                    <th>Picture</th>
                    <th>Caption</th>
                    <th>Actions</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                      <pin pins="pins"  v-on:errorshappened="addError" v-on:passed="showSuccess" errors="errors" v-for="pin in pins" :pin="pin" :csrftoken="csrftoken" :key="pin.id"></pin>
                </tbody>
            </table>
        </div>
        <errors :errors="errors"></errors>
    </div>

</template>
<script>
    import bus from './../Bus.vue';

    export default {
        name : 'Dashboard',
        components: {
            'errors': require('./../dashboard/errors.vue'),
            'pin': require('./../dashboard/Pin.vue'),
            'scheduledTime': require('./../facebook/scheduledTime.vue'),
            'success': require('./../dashboard/success.vue'),
            'iFramePin': require('./../pinterest/iFramePin.vue'),
        },
        data() {
            return {
                errors: [],
                pins: {},
                success_messages: [],
                in_progress :false,
                loading_pins :true,
                changed :false,
                status : {
                    'message' : null,
                    'class_name' : null
                },
                board_name : null
            }
        },
        props: ['csrftoken', 'connectUrl', 'name'],
        methods: {
            addError(error) {
                $('#myModal').modal('show');
                this.errors.push(error);
                this.success_messages = [];
            },
            showSuccess(message) {
                this.success_messages.push(message.data);
            },
            cancel(el) {
                this.$parent.cancel(el);
            },
            synchronize() {
                //initial state
                this.success_messages = [];
                this.setStatus('Synchronizing . . .', 'alert-info');
                axios
                    .get('/synchronize').then(response => {
                        this.getPins();
                        this.setStatus(response.data.message, 'alert-success');
//                        this.success_messages.push(response.data.message);
//                        this.$emit('success_message', this.success_messages);
                    })
                    .catch(errors => {
                        this.setStatus(errors.response.data, 'alert-danger');
                    });
                ;
            },
            getPins() {
                axios.get('/pins')
                    .then(response => {
                        console.log(response.data);
                        this.pins = response.data.pins;

                        this.board_name = response.data.board_name;
                        this.loading_pins = false;
                    });
            },
            setStatus(message, class_name) {
                this.status.message = message;
                this.status.class_name = class_name;
            }
        },
        beforeCreate() {
            this.loading_pins = true;
        },
        created() {
            this.getPins();
        },
        mounted() {
            this.$root.$emit('connect', {'url' : this.connectUrl, 'name' : this.name});

            //pages
            axios.get('/pages/get')
                .then(response => {
                    bus.pages = response.data.pages;
                    bus.activated_page = response.data.activated;
//                    bus.active_page_name = bus.pages[response.data.activated].name;
                    bus.token = response.data.token;
                    this.$root.$emit('pages',bus);
                });
            //boards
            axios.get('/boards/get')
                .then((response) => {
                    bus.boards = response.data.boards;
                    bus.active_board = response.data.active_pages;
                    this.$root.$emit('boards',bus);

                });
            this.$root.$on('boardChanged', (text) => {
                this.getPins();
            })
        }
    }
</script>
<style>
    .empty {
        text-align: center;
    }
</style>