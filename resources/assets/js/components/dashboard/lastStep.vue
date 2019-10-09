<template lang="html">
    <div id="lastStep" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header"><h4>Welcome to Syncy {{ name }}</h4> </div>
                    <div class="modal-body">
                        <div class="card-block">
                             <div class="alert alert-success" role="alert"> Done! Happy Syncing ... </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" @click="cancel()" class="btn btn-alert">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name : 'lastStep',
        data() {
            return {
                name : null
            }
        },
        mounted () {
            this.$root.$on('connect', (obj) => {
                this.name = obj.name;
            });
            jQuery('#lastStep').modal('show');
        },
        methods : {
            cancel() {
                this.$parent.cancel(jQuery('#lastStep'));
            }
        }
    }
</script>