<template lang="html">
    <div id="CropAndShare" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                <h4>Crop it and share to Instagram</h4>
                </div>
                    <div class="modal-body">
                    <div v-show="status.message !== null" :class="'alert '+status.class_name" role="alert">
                        {{ status.message }}
                    </div>
                        <form>
                            <div class="form-group">
                                <textarea v-model="caption" class="form-control" id="message-text">
                                    {{ caption }}
                                </textarea>
                            </div>
                        </form>
                        <div style="max-width: 900px; display: inline-block;">
                            <vue-cropper
                                ref='cropper'
                                alt=''
                                :guides='true'
                                :view-mode="2"
                                :auto-crop-area="0.5"
                                :min-container-width="250"
                                :min-container-height="500"
                                :check-cross-origin="false"
                                :background="true"
                                :rotatable="true"
                                :src="imgSrc"
                                :aspect-ratio="1/1"
                                :cropmove="cropImage" >
                            </vue-cropper>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" @click="share()" class="btn btn-alert">Share</button>
                    <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueCropper from 'vue-cropperjs';
    import bus from './../Bus.vue';

    export default {
        name : 'CropAndShare',
//        data() {
//            return {
//                name : null
//            }
//        },
//        mounted () {
//            jQuery('#CropAndShare').modal('show');
//        },
//        methods : {
//            cancel() {
//                this.$parent.cancel(jQuery('#CropAndShare'));
//            }
//        }
//
        /**************/
        data () {
            return {
                imgSrc: '',
                cropImg: '',
                img_obj : {},
                refs : {},
                dimensions : {},
                status : {
                    'message' : null,
                    'class_name' : null
                },
                pin : {},
                caption : null
            };
        },
        mounted () {
            this.pin = bus.pin;
            this.caption = this.pin.caption;
            jQuery('#CropAndShare').modal('show');
            let img_obj = JSON.parse(bus.pin.image_src);
            this.imgSrc = '/image?url=' + encodeURIComponent(img_obj.original.url) +'&v=' + Math.floor(Date.now() / 1000);
            this.$refs.cropper.replace(this.imgSrc);
        },
        methods: {
            setStatus(message, class_name) {
                this.errors = [];
                this.status.message = message;
                this.status.class_name = class_name;
                $(".modal").animate({ scrollTop: 0 }, "slow");
            },
            cropImage () {
                this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
                this.dimensions = this.$refs.cropper.getCropBoxData();
            },
            share() {
                this.setStatus('Sharing to your Instagram account . . .', 'alert-info');
                axios.post('/instagram/share', {
                    img : this.cropImg, dimensions: this.dimensions, caption : this.caption })
                .then(response => {
                    this.setStatus('The post is shared successfully!', 'alert-success');
                }).catch(error => {
                    this.setStatus('Error : ' + error.data, 'alert-danger');
                });
            },
            rotate () {
                this.refs.cropper.rotate(90);
            },
            cancel() {
                this.$parent.cancel(jQuery('#CropAndShare'));
            }
        }
    }
</script>