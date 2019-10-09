<template lang="html">
    <div id="home-page">
        <h1>Bring your pins to your social media networks</h1>

        <h5>Contact us</h5>
        <div class="main-login main-center">

            <form class="form-horizontal"id="contactForm" name="contactForm"  method="post" action="/contact">
                <!--Errors-->
                <div class="form-group">
                    <ul v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
                        <span v-show="status.class_name == 'alert-success'">{{ status.message }}</span>
                        <li v-for="error in errors" :error="error" role="alert"> {{ error.toString() }}  </li>
                    </ul>
                </div>
                <div class="form-group">
                    <input name="_token" type="hidden" :value="csrf" />
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="name" id="name"  placeholder="Enter your Name"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" name="email" id="email"  placeholder="Enter your Email"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="cols-sm-10">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope" aria-hidden="true"></i></span>
                            <textarea class="form-control" name="message" id="message"  placeholder="Enter your Messgae"/>
                        </div>
                    </div>
                </div>
                <div class="form-group ">
                    <button @click="send()" type="button" class="btn btn-primary btn-lg btn-block login-button">Send</button>
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
                errors : [],
                status : {
                    'message' : null,
                    'class_name' : null
                }
            }
        },
        methods : {
            send() {
                let data = new FormData(document.getElementById('contactForm'));
                axios.post('/contact', data )
                    .then(response => {
                        this.status.message = 'Message sent successfully, we\'ll be in touch soon!';
                        this.status.class_name = 'alert-success';
                    })
                    .catch(errors => {
                        this.errors = Object.values(errors.response.data.errors);
                        this.status.class_name = 'alert-danger';
                        this.status.message = 'Please fix the following errors:';
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