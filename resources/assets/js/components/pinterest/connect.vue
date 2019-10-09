<template lang="html">
    <div id="pinterestConnect" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <!--<div class="progress-bar" role="progressbar" style="width: 25%; margin-bottom: 17px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="25">25%</div>-->
                <div class="modal-header"><h4>Step 1 : Attach your pinterest account to Syncy</h4> </div>
                    <div class="modal-body">
                        <div class="card-block">
                        <p> Connect your pinterest account to import your boards and your pins
                            </p>
                                <button @click="click()" type="button" class="btn btn-default btn-lg pinterest custom">
                                    <span>Get started</span>
                                </button>
                        </div>
                    </div>
                <div class="modal-footer">
                </div>

            </div>

        </div>
    </div>
</template>
<script>
    export default {
        name : 'pinterestConnect',
        data() {
            return {
                name : null
            }
        },
        mounted () {
            jQuery('#pinterestConnect').modal('show');
        },
        methods : {
            click () {
                location.href= window.Laravel.url;
            },
            cancel() {
                this.$parent.cancel(jQuery('#pinterestConnect'));
            }
        }
    }
</script>