<template lang="html">
    <div id="scheduled_time" class="modal fade" role="dialog"  data-backdrop="static" data-keyboard="false">
         <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"> Schedule a date time for your post</div>
                <div class="modal-body">
                <form>
                    <div class="form-group">
                        <textarea v-model="caption" class="form-control" id="message-text"> {{ caption }}</textarea>
                    </div>

                </form>
                  <div class="pin_img" style="height: 400px" >
                    <img v-if="img_url == null" src="/img/loader.gif"/>
                  </div>

                    <div class="modal-footer">
                     <div v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
                        {{ status.message }}
                    </div>
                        <!--Time Stamp-->
                        <button type="button"  id="datetimePicker" class="btn">{{ timeText }}</button>
                        <ul class="btn nav">
                         <!--v-show="activated_page !== null"-->
                         <!--&gt;-->
                            <li class="dropdown">
                            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-alert dropdown-toggle">
                                {{  page.name === null ? 'Facebook page' :  page.name }}
                                <span class="caret"></span>
                            </button>
                                <ul class="dropdown-menu">
                                    <li v-for="page in pages" :key="page.page_id">
                                        <a @click="switchPage(page.id, page.name)" href="#" >
                                            {{ page.name }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <button type="button" @click="share()" class="btn btn-success">Share</button>
                        <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import bus from './../Bus.vue';
    import datetimepicker from './../../jquery.datetimepicker.full.min.js';
    export default {
        name : 'scheduledTime',
        data () {
            return {
                errors : [],
                pages : {},
                activated_page : null,
                active_page_name : null,
                data: {},
                timeText : 'Scheduled time',
                caption : null,
                img_url : null,
                status : {
                    'message' : null,
                    'class_name' : null
                },
                page : {
                    name : null,
                    id : null
                },
                post_date : null
            }
        },
        mounted() {
                jQuery('#scheduled_time').modal('show');
                //fb pages
                this.pages = bus.pages;
//                this.active_page_name = bus.active_page_name;
                this.activated_page = bus.activated_page;
                //pin details
                let img_obj = JSON.parse(bus.pin.image_src);
                this.img_url = img_obj.original.url;
                this.caption = bus.pin.caption;
                //Jquery Time picker
                this.time_picker(jQuery('#datetimePicker'));
                jQuery('.pin_img').css('background-image', 'url(' + this.img_url+ ')');
        },
        methods : {
            cancel() {
                this.$parent.cancel(jQuery('#scheduled_time'));
            },
            share() {
                if(this.post_date === null) {
                    this.setStatus('Error : Please chose a scheduling time for your post', 'alert-danger');
                    return false;
                }
                if(this.page.id === null) {
                    this.setStatus('Error : Please chose a destination page', 'alert-danger');
                    return false;
                }
                this.setStatus('Sharing your post to ' + this.page.name + " . . .", 'alert-info');

                let post = {
                    "page_id" : this.page.id,
                    "_token" : window.Laravel.csrfToken,
                    "caption" : bus.caption,
                    "pin_id" : bus.pin_id,
                    "scheduled_at" : this.post_date,
                };
                console.log(post);
                axios
                    .post('/share', post, { headers: {'X-CSRF-TOKEN': this.csrftoken } })
                    .then(response => {
                        this.setStatus('Yaaay! Shared successfully to ' + this.page.name, 'alert-success');
                    })
                    .catch(error=> {
                        this.setStatus('Error :' + error.response.data, 'alert-danger');
                    });
            },
            time_picker(el) {
                let dt = new Date();
                console.log(dt);
                el.datetimepicker({
                    format:'d-m-Y H:i:s',
                   // minDate: dt,
                    defaultDate: dt,
                    onChangeDateTime:function(dp,$input){
                        this.post_date = this.timeText = $input.val();
                    }.bind(this)
                });
            },
            getfbPages() {
                axios.get('/pages/get')
                    .then(response => {
                        this.pages = response.data.pages;
                        this.activated_page = response.data.activated;
                        this.active_page_object = bus.pages[this.activated_page];
                    });
            },
            switchPage(page_id, page_name) {
                this.page.id = page_id;
                this.page.name = page_name;
            },
            setStatus(message, class_name) {
                this.status.message = message;
                this.status.class_name = class_name;
            }
        }
    }
</script>
<style>
    .xdsoft_datetimepicker {
        zoom: 1.1;
    }
</style>