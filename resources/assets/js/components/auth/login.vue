<template lang="html">
    <div id="home-page">
        <h1>Bring your pins to your social media networks</h1>
        <h5>Welcome back!</h5>
        <div class="main-login main-center">

            <form action="/login" method="post" id="loginForm" class="form-horizontal">
                <div class="form-group">
                    <!--Errors-->
                    <ul class="alert alert-danger" v-show="errors.length > 0">
                        <li v-for="error in errors" :error="error" role="alert"> {{ error.toString() }} </li>
                    </ul>
                </div>

                <input type="hidden" name="_token" :value="csrf"/>
                <div class="form-group">

                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                            <input class="form-control" v-bind:class="{ 'alert-danger': errors.email }" name="email" id="inputEmail"  placeholder="Email address"  required="" autofocus="" type="email"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">

                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock" aria-hidden="true"></i></span>
                            <input type="password" class="form-control" name="password" id="inputPassword"  placeholder="Password"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input value="remember-me" type="checkbox">
                            <span>
                                Remember me
                            </span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                     <button @click="login()" type="button" class="btn btn-primary btn-lg btn-block login-button">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                csrf : window.Laravel.csrfToken,
                errors : []

            }
        },
        mounted () {
            this.$nextTick(() => {
                this.$parent.initParticleJS()
            })
        },
        methods : {
            login() {
                axios.post('/login', new FormData(document.getElementById('loginForm')) )
                    .then(response => {
                        location.href = '/dashboard';
                    })
                    .catch(error => {
                        this.errors = Object.values(error.response.data.errors);
                    });
            }
        }
    }
</script>
<style>
    .checkbox {
        float: left;
    }
    .checkbox span {
        color : #555555;
    }
</style>