<template lang="html">
    <div id="TimelinePost" class="modal fade bd-example-modal-lg" role="dialog">
        <div class="modal-dialog" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header"><h4>Post to your timeline</h4> </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <textarea v-model="caption" class="form-control" id="message-text"> {{ caption }}</textarea>
                            </div>
                        </form>
                          <div class="pin_img" style="height: 400px" >
                            <img v-if="img_url == null" src="/img/loader.gif"/>
                          </div>
                    </div>
                    <div class="modal-footer">
                    <div v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
                        {{ status.message }}
                    </div>
                    <ul class="btn nav">
                            <li class="dropdown">
                            <button type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" class="btn btn-alert dropdown-toggle">
                                {{ privacy.text  }}
                                <span class="caret"></span>
                            </button>
                                <ul class="dropdown-menu">
                                    <li  v-for="type in privacy_types"  :type="type" :key="type.id" ><a @click="privacyChoice(type)">{{ type.text }}</a> </li>
                                </ul>
                            </li>
                        </ul>
                    <button type="button" @click="share()" class="btn btn-success">Share</button>
                    <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import bus from './../Bus.vue';
    export default {
        name : 'TimelinePost',
        data() {
            return {
                img_url : null,
                pin : {},
                caption : null,
                status : {
                    'message' : null,
                    'class_name' : null
                },
                privacy : {'id' : null , 'text' : 'Privacy'} ,
                privacy_types : [
                    {'id' :'ALL_FRIENDS' , 'text' : 'Friends'},
                    {'id' :'EVERYONE' , 'text' : 'Public'},
                    {'id' :'FRIENDS_OF_FRIENDS' , 'text' : 'Friends of Friends'},
                    {'id' :'SELF' , 'text' : 'Only Me'}
                ]
            }
        },
        mounted () {
            jQuery('#TimelinePost').modal('show');
            let img_obj = JSON.parse(bus.pin.image_src);

            this.img_url = img_obj.original.url;
            this.caption = bus.pin.caption;
            //load image
            jQuery('.pin_img').css('background-image', 'url(' + this.img_url+ ')');
        },
        methods : {
            cancel() {
                this.$parent.cancel(jQuery('#TimelinePost'));
            },
            share () {
                if (this.privacy.id === null) {
                    this.setStatus('Error : The privacy is not configured!', 'alert-danger');
                    return false;
                }
                bus.pin.privacy = this.privacy.id;
                this.setStatus('Sharing ...', 'alert-info');
                axios.post('/timeline/post', bus.pin)
                    .then(response => {
                        this.setStatus('Post shared!', 'alert-success');
                    })
                    .catch(error=> {
                        this.setStatus('Error :' + error.response.data, 'alert-danger');
                     });
            },
            privacyChoice (type) {
                this.privacy = type;
            },
            setStatus(message, class_name) {
                this.status.message = message;
                this.status.class_name = class_name;
            }

        }
    }
</script>
<style>
    .pin_img {
        max-height:400px;
        background-size: 100%;
    }
</style>