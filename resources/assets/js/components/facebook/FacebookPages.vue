<template lang="html">
    <div id="pages-list" class="modal fade" role="dialog">
        <div class="modal-dialog">
             <!--<div class="progress"  v-if="activated === null">-->
               <!--<div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>-->
             <!--</div>-->
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                 Manage Your Facebook Pages</div>
                <div class="modal-body">
                    <div v-show="success" class="alert alert-success" role="alert">
                        Facebook page has changed successfully!
                    </div>
                    <div v-show="in_progress" class="alert alert-info" >
                        Switching the Facebook page . . .
                    </div>
                    <form method="post" id="facebook_pages" action="facebook/configure" >
                        <div class="row text-center text-lg-left">
                            <div pages="pages" v-for="page in pages" class="col-lg-2 col-md-4 col-xs-6">
                                <input type="radio" class="input_hidden" :checked="page.id === activated" :id="page.id" name="page" :value="page.id" />
                                <a @click="chosePage(page.id)" href="#" class="d-block mb-4 h-100" data-toggle="tooltip" data-placement="top" :title="page.name">
                                    <img :data-page="page.id"  v-bind:class="{selected: page.id == activated}" class="img-fluid img-thumbnail" :src="getAvatar(page.id, token)" :alt="page.name">
                                    <span v-show="page.id === activated" class="active_item glyphicon glyphicon-ok"></span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="save()" class="btn btn-success">Save</button>
                    <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import bus from './../Bus.vue';
    export default {
        name : 'FacebookPages',
        data() {
            return {
                formErrors: {},
                pages: {},
                token : '',
                activated: null,
                success: false,
                in_progress :false
            }
        },
        methods: {
            getAvatar(page_id, access_token) {
                return 'https://graph.facebook.com/' + page_id +  '/picture?type=small&access_token=' + access_token;
            },
            chosePage: function (page) {
                jQuery('.img-thumbnail').removeClass('selected');
                jQuery('[data-page='+page+']').addClass('selected');
                jQuery('#'+page).prop('checked', true);
                this.activated = page;
            },
            save: function () {
                this.success = false;
                this.in_progress = true;
                let fData = new FormData(document.getElementById('facebook_pages'));
                axios.post('configure', fData)
                    .then(response => {
                        this.success = true;
                        this.in_progress = false;
                        bus.activated_page = this.activated;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            cancel() {
                this.$parent.cancel(jQuery('#pages-list'));
            }
        },
        created () {
            this.pages = bus.pages;
            this.token = bus.token;
            this.activated = bus.activated_page;
        },
        updated() {
            jQuery('[data-toggle="tooltip"]').tooltip();
        },
        mounted() {
            this.$root.$on('pages', (data) => {
                this.pages = data.pages;
                this.token = data.token;
                this.activated = data.activated_page;
            });
            jQuery('#pages-list').modal('show');
        }

    }
</script>
<style lang="css">

    .input_hidden {
        position: absolute;
        left: -9999px;
    }

    .selected {
        background-color: #4CAF50;
    }

    .bd-example label {
        display: inline-block;
        cursor: pointer;
    }


    .bd-example label:hover {
        background-color: #8bc34a;
    }

    .bd-example label img {
        padding: 3px;

    }
    .btn {
        /*position: relative;*/
        /*left: 83%;*/
    }

</style>