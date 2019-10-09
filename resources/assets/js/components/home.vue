<template>
        <div>
            <div class="home-page">
                <h1>Bring your Pinterest to your social media networks</h1>
                <h5>
                    The fastest way to upload your Pinterest images to all your Social Media
                </h5>
            </div>
            <register></register>
        </div>
</template>
<script>
    import Register from "./auth/register";

    export default {
        name : 'home',
        components : {
            Register,
            'errors': require('./auth/register.vue'),
        },
        data() {
            return {
                url : null
            }
        },
        mounted () {
            this.$nextTick(() => {
                this.$parent.initParticleJS()
            })
        },
        methods: {
              click () {
                  location.href= window.Laravel.url;
              },
        }
    }
</script>
<style>

</style>