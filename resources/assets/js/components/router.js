import Vue from 'vue'
import VueRouter from 'vue-router'
import FacebookPages from './facebook/FacebookPages.vue'
import PinterestBoards from './pinterest/PinterestBoards.vue'
import scheduledTime from './facebook/scheduledTime.vue'
import connectFacebook from './facebook/connectFacebook.vue'
import pinterestConnect from './pinterest/connect.vue'
import lastStep from './dashboard/lastStep.vue'
import iFramePin from './pinterest/iFramePin.vue'
import TimelinePost from './facebook/TimelinePost.vue'
import login from './auth/login.vue'
import register from './auth/register.vue'
import contact from './auth/contact.vue'
import home from './home.vue'
import profile from './dashboard/profile.vue'
import CropAndShare from './instagram/CropAndShare.vue'
import InstagramRegister from './instagram/Register.vue'

// Install Router
Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes:  [
        {
            path: '/',
            name: 'home',
            component : home,
            props: true
        },
        {
            path : '/profile',
            name : 'profile',
            component : profile
        },
        {
            path: '/facebook/pages',
            name: 'FacebookPages',
            component: FacebookPages
        },
        {
            path: '/pinterest/boards',
            name: 'PinterestBoards',
            component: PinterestBoards
        },
        {
            path : '/schedule',
            name : 'scheduledTime',
            component : scheduledTime
        },
        {
            path : '/facebook/authorize',
            name : 'connectFacebook',
            component : connectFacebook
        },
        {
            path : '/pinterest/connect',
            name : 'pinterestConnect',
            component : pinterestConnect
        },
        {
            path : '/done',
            name : 'lastStep',
            component : lastStep
        },
        {
            path : '/pin/:id',
            name : 'iFramePin',
            component : iFramePin,
            props: true
        },
        {
            path : '/share/:id',
            name : 'TimelinePost',
            component : TimelinePost,
            props: true
        },
        {
            path : '/login',
            name : 'login',
            component : login,
        },
        {
            path : '/register',
            name : 'register',
            component : register,
        },
        {
            path : '/instagram/share',
            name : 'CropAndShare',
            component : CropAndShare,
        },
        {
            path : '/instagram',
            name : 'InstagramRegister',
            component : InstagramRegister,
        },
        {
            path : '/contact',
            name : 'Contact',
            component : contact,
        }
    ]
});

// router.replace({ path: '', redirect: '/' })
export default router
