import VueRouter from './components/router'


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('jquery');
require('./bootstrap');

Vue.config.devtools = true;
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import bus from './components/Bus.vue';

const app = new Vue({

    el: '#app',
    components : {
        'success': require('./components/dashboard/success.vue'),
        'FacebookPages': require('./components/facebook/FacebookPages.vue'),
        'lastStep': require('./components/dashboard/lastStep.vue'),
        'dashboard': require('./components/dashboard/Dashboard.vue')
    },
    router: VueRouter,
    data: {
        board : '',
        success_messages : [],
        pins : [],
        errors: [],
        csrftoken: window.Laravel.csrfToken,
        showModal: false,
    },
    methods: {
        cancel(el) {
            el.modal('hide');
            this.$router.push('/dashboard');
        },
        addError(error) {
            $('#myModal').modal('show');
            this.errors.push(error);
            this.success_messages = [];
        },
        showSuccess(message) {
            this.success_messages.push(message.data);
        },
        initParticleJS ()
        {
            particlesJS("particlesJs", {
                particles: {
                    number: {
                        value: 300,
                        density: {
                            enable: !0,
                            value_area: 800
                        }
                    },
                    color: {
                        value: "#ffffff"
                    },
                    shape: {
                        type: "circle",
                        stroke: {
                            width: 4,
                            color: "rgba(255, 255, 255, .01)"
                        },
                        polygon: {
                            nb_sides: 5
                        }
                    },
                    opacity: {
                        value: .2,
                        random: !1,
                        anim: {
                            enable: !1,
                            speed: 1,
                            opacity_min: .6,
                            sync: !1
                        }
                    },
                    size: {
                        value: 1,
                        random: !0,
                        anim: {
                            enable: !1,
                            speed: 40,
                            size_min: .1,
                            sync: !1
                        }
                    },
                    line_linked: {
                        enable: !0,
                        distance: 150,
                        color: "#ffffff",
                        opacity: .15,
                        width: 1
                    },
                    move: {
                        enable: !0,
                        speed: 1,
                        direction: "none",
                        random: !1,
                        straight: !1,
                        out_mode: "out",
                        attract: {
                            enable: !1,
                            rotateX: 600,
                            rotateY: 1200
                        }
                    }
                },
                interactivity: {
                    detect_on: "canvas",
                    events: {
                        onhover: {
                            enable: !1,
                            mode: "repulse"
                        },
                        onclick: {
                            enable: !0,
                            mode: "push"
                        },
                        resize: !0
                    },
                    modes: {
                        grab: {
                            distance: 400,
                            line_linked: {
                                opacity: 1
                            }
                        },
                        bubble: {
                            distance: 400,
                            size: 40,
                            duration: 2,
                            opacity: 8,
                            speed: 3
                        },
                        repulse: {
                            distance: 200
                        },
                        push: {
                            particles_nb: 4
                        },
                        remove: {
                            particles_nb: 2
                        }
                    }
                },
                retina_detect: !0
            });
        }
    },
    mounted() {

    },
    events : {
        /**/
        'cancel': function (){
            this.cancel();
        }
    }
}).$mount('#app');