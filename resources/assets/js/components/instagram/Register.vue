<template lang="html">
    <div id="InstagramRegister" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <img src="/img/loader.gif" v-if="loading" />
                        <div id="register" v-if="unregistered" >
                            <div v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
                                {{ status.message }}
                                <div v-show="errors.length > 0" v-for="error in errors" :error="error"> {{ error.toString() }}  </div>
                            </div>
                            <div class="alert alert-success" v-if="success" > Instagram credentials saved </div>
                            <form action="/login" method="post" id="instaCredentials" class="form-signin">
                                <input type="hidden" name="_token" :value="csrf"/>
                                <input v-model="username" name="username" id="inputUsername" class="form-control" placeholder="Username" required="" autofocus="">
                                <input v-model="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="" type="password">
                                <button @click="save()" type="button" class="btn btn-primary btn-lg btn-block login-button">Save</button>
                            </form>
                        </div>
                        <div class="profile" v-if="!unregistered && !loading">
                            <div class="col-sm-12">
                                <div class="col-xs-12 col-sm-12">
                                    <img class="profile-img" :src="profile.profile_pic_url" />
                                    <h2> {{ profile.username }} </h2>
                                    <p> {{ profile.email }} </p>
                                </div>
                            </div>
                            <div class="col-xs-12 divider text-center" v-if="!unregistered">
                                <div class="col-xs-12 col-sm-6 emphasis">
                                    <button @click="openProfile()" class="btn btn-success btn-block"><span class="fa fa-plus-circle"></span> View profile </button>
                                </div>
                                <div class="col-xs-12 col-sm-6 emphasis">
                                    <button @click="disconnect()"  class="btn btn-info btn-block"><span class="fa fa-user"></span> Disconnect Instagram account </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import bus from './../Bus.vue';
    export default {
        name : 'InstagramRegister',
        data() {
            return {
                csrf : window.Laravel.csrfToken,
                username : null,
                password: null,
                errors: [],
                success : false,
                status : {
                    'message' : null,
                    'class_name' : null
                },
                router : this.$router,
                unregistered : false,
                profile : {},
                loading : true
            }
        },
        mounted () {
            jQuery('#InstagramRegister').modal('show');
            this.getProfile();
        },
        methods : {
            cancel() {
                this.$parent.cancel(jQuery('#InstagramRegister'));
            },
            save() {
                this.setStatus('Validating and checking the Instagram credentials ... ', 'alert-info');
                //save Instagram credentials
                axios.post('/instagram/connect', {username : this.username, password: this.password })
                    .then(response => {
                        this.setStatus("Your instagram account is connected!", 'alert-success');
                        this.getProfile();
                    })
                    .catch(errors => {
                        this.setStatus("", 'alert-danger');
                        this.errors = errors.response.data;
                    });
            },
            getProfile() {
                axios.get('/instagram/account')
                    .then(response => {
                        this.loading = false;
                        this.profile = response.data;
                        this.unregistered = false;
                    })
                    .catch(errors => {
                        this.loading = false;
                        this.unregistered = true;
                    });
            },
            setStatus(message, class_name) {
                this.errors = [];
                this.status.message = message;
                this.status.class_name = class_name;
            },
            openProfile() {
                window.open('https://www.instagram.com/' + this.profile.username, '_blank');
            },
            disconnect() {
                this.loading = true;
                axios.get('/instagram/disconnect')
                    .then(response => {
                        this.profile = response.data;
                        this.unregistered = true;
                        this.loading = false;
                        this.setStatus("Instagram account is disconnected", 'alert-info');
                    })
                    .catch(errors => {
                        this.loading = false;
                    });
            }
        }
    }
</script>