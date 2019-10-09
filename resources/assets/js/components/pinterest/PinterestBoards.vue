<template lang="html">
    <div id="boards-list" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">

            <div class="modal-content">
                <!--
                <!--<div  v-if="active_board === null" class="progress-bar" role="progressbar" style="width: 25%; margin-bottom: 17px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>&ndash;&gt;-->
                <div class="modal-header">
                <span v-if="active_board === null"> Step 2 : Choose the Pinterest board to import to Syncy </span>
                Manage your Pinterest boards
                <a @click="synchronize()">
                    <span class="glyphicon glyphicon-refresh pull-right" data-toggle="tooltip" data-placement="top"
                    title="Import latest boards" aria-hidden="true"></span>
                </a></div>
                <div class="modal-body">
                    <div errors="errors" v-show="errors.length > 0" class="alert alert-danger" role="alert">
                        <span errors="errors" v-for="error in errors"> {{ error.toString() }} </span>
                    </div>

                    <div v-show="success" class="alert alert-success" role="alert">
                        Pinterest settings has been configured successfully
                    </div>
                    <div v-show="in_progress" class="alert alert-info" >
                         {{ txt_msg }}
                    </div>
                     <div >
                        <form method="post" id="pinterest_boards" >
                            <div class="row text-center text-lg-left">
                                <input type="hidden" name="_token" :value="csrf">
                                <div boards="boards" v-for="board in boards" class="col-lg-2 col-md-4 col-xs-3">
                                    <input type="radio" class="input_hidden" :checked="board.id === active_board" :id="board.id" name="board" :value="board.id" />
                                    <a @click="choseBoard(board.id)" href="#" class="d-block mb-4 h-100" data-toggle="tooltip" data-placement="top" :title="board.name">
                                        <img height="90" width="90" :data-board="board.id"  v-bind:class="{selected: board.id == active_board}" class="img-fluid img-thumbnail" :src="displayImg(board.image.small.url)" :alt="board.name">
                                        <span v-show="board.id === active_board" class="active_item glyphicon glyphicon-ok"></span>
                                    </a>
                                    <div class="caption">
                                        <h5>{{ board.name }}</h5>
                                    </div>
                                </div>
                            </div>
                         </form>
                     </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="save()" class="btn btn-success"><span v-if="in_progress">Saving</span><span v-else>Save</span> </button>
                    <button v-if="success" type="button" @click="synchronize()" class="btn btn-success"><span v-if="in_progress">Importing</span><span v-else>Import</span> </button>
                    <button v-if="active_board !== null" type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import bus from './../Bus.vue';
    import Dashboard from './../dashboard/Dashboard.vue';
    export default {
        name: 'PinterestBoards',
        data() {
            return {
                boards: {},
                active_board: null,
                errors : [],
                success: false,
                in_progress : false,
                txt_msg : null,
                msg : {
                    switching : 'Switching the Pinterest board . . .',
                    synchronize : 'Importing latest Pinterest boards . . .'
                },
                csrf : window.Laravel.csrfToken
            }
        },
        methods: {
            choseBoard: function (board) {
                jQuery('.img-thumbnail').removeClass('selected');
                jQuery('[data-board='+board+']').addClass('selected');
                jQuery('#'+board).prop('checked', true);
                this.active_board = board;
            },
            save: function() {
                if(this.in_progress) {
                    return false;
                }
                let data = new FormData(document.getElementById('pinterest_boards'));
                this.in_progress = true;
                this.success = false;
                this.txt_msg = this.msg.switching;
                axios
                    .post('save', data, { headers: { 'X-CSRF-TOKEN': window.Laravel.csrfToken } })
                    .then(response => {
                        this.success = true;
                        this.errors = {};
                        this.$root.$emit('boardChanged');
                        bus.active_board = this.active_board;
                        this.in_progress = false;
                        //send to facebook connection step
                        if(response.data.token === null) {
                            location.href = '/facebook/authorize';
                        }
                    })
                    .catch(errors => {
                       // this.errors = errors.response.data.board;
                    });
            },
            displayImg(img_url) {
                return (img_url === null) ? '/img/pinterest-.png' : img_url.replace('30x30', '90x90');
            },
            cancel() {
                this.$parent.cancel(jQuery('#boards-list'));
            },
            synchronize() {
                this.in_progress  = true;
                this.success = false;
                this.errors = {};
                this.txt_msg      = this.msg.synchronize;
                axios
                    .get('/boards/get?synchronize=true').then(response => {
                        this.boards = response.data.boards;
                        this.active_board = response.data.active_pages;
                    })
                    .catch(error => {
                        console.log( error.response.data.error);
                        this.errors = [error.response.data.error];
                        this.in_progress  = false;
                    });
            }
        },
        created() {
            this.boards = bus.boards;
            this.active_board = bus.active_board;
        },
        updated() {
            jQuery('[data-toggle="tooltip"]').tooltip();
        },
        mounted() {
            this.$root.$on('boards', (data) => {
                this.boards = data.boards;
                this.active_board = data.active_board;
            });
            jQuery('#boards-list').modal('show');
        }
    }

</script>
<style>
    .input_hidden {
        position: absolute;
        left: -9999px;
    }
    .col-lg-2 {
        margin-top: 15px;
    }
</style>