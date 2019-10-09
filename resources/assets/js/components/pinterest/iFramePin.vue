<template lang="html">
    <div id="iFramePin" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">

            <div class="modal-body">
            <button @click="close()" type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
                <a id="thepin" data-pin-do="embedPin" data-pin-width="medium" :href="'https://www.pinterest.com/pin/'+id">
                    <img src="/img/loader.gif"/>
                </a>
            </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name : 'iFrame',
        props: ['id'],
        data() {
            return {
                loaded : false
            }
        },
        mounted () {
            jQuery('#iFramePin').modal('show');
            this.$parent.loadJs('//assets.pinterest.com/js/pinit.js');
        },
        methods : {
            close() {
                this.cancel(jQuery('#iFrame'));
            },
            loadJs(url) {
                jQuery.ajax({
                    url: url,
                    dataType: 'script',
                    success : function () {
                        this.loaded = true;
                        if (PinUtils !== undefined ) {
                            PinUtils.build();
                        }
                    }.bind(this),
                    async: true
                });
            }
        }
    }
</script>
<style>
    #iFramePin {
        text-align: center;
        width: 80%;
        margin-left: auto;
        margin-right: auto;
    }
    .close {
        position: relative;
        top: -10px;
        font-size: 40px;
    }
</style>