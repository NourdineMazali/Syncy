<template lang="html">
    <div class="main-login main-center">

        <!--Errors-->
        <ul class="nav nav-tabs alert alert-danger" v-show="errors.length > 0">
             <li v-for="error in errors" :error="error" role="alert"> {{ error.toString() }} </li>
        </ul>

        <form class="form-horizontal"id="registerForm" name="registerForm"  method="post" action="/register">

            <div class="form-group">
                <input name="_token" type="hidden" :value="csrf" />
                <!--<label for="name" class="cols-sm-2 control-label">Your Name</label>-->
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" name="name" id="name"  placeholder="Enter your Name"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <!--<label for="email" class="cols-sm-2 control-label">Your Email</label>-->
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" name="email" id="email"  placeholder="Enter your Email"/>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <!--<label for="password" class="cols-sm-2 control-label">Password</label>-->
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" name="password" id="password"  placeholder="Enter your Password"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <!--<label for="confirm" class="cols-sm-2 control-label">Confirm Password</label>-->
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock" aria-hidden="true"></i></span>
                        <input type="password" class="form-control" name="password_confirmation" id="confirm"  placeholder="Confirm your Password"/>
                    </div>
                </div>
            </div>

            <div class="form-group ">

                <button @click="register()" type="button" class="btn btn-primary btn-lg btn-block login-button">Register</button>
            </div>
            <div class="login-register">
                <router-link to="/login">Login </router-link>
             </div>
        </form>
	</div>
</template>
<script>
    export default {
        name : 'register',
        data() {
            return {
                csrf : window.Laravel.csrfToken,
                errors : []
            }
        },
        methods : {
            register() {
              let data = new FormData(document.getElementById('registerForm'));
                axios.post('/register', data )
                    .then(response => {
                         location.href = '/pinterest/connect';
                    })
                    .catch(error => {
                        this.errors = Object.values(error.response.data.errors);
                    });
            }
        }
    }
</script>
<style>
    .form-group{
        margin-bottom: 15px;
    }

    label{
        margin-bottom: 15px;
    }

    input,
    input::-webkit-input-placeholder {
        font-size: 11px;
        padding-top: 3px;
    }

    .main-login{
        background-color: #fff;
        /* shadows and rounded borders */
        -moz-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);

    }

    .main-center{
        margin-top: 30px;
        margin: 0 auto;
        max-width: 330px;
        padding: 40px 40px;

    }

    .login-button{
        margin-top: 5px;
    }

    .login-register{
        font-size: 11px;
        text-align: center;
    }

</style>