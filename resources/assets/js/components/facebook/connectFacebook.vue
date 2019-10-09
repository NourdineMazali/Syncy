<template lang="html">
    <div id="connectFacebookAccount" class="modal fade" role="dialog">
        <div class="modal-dialog">

        <!-- Modal content-->
            <!--<div class="progress-bar" role="progressbar" style="width: 50%; margin-bottom: 17px;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50%</div>-->
            <div class="modal-content">
                <div class="modal-header">
                    <span>Step 3 : Connect your Facebook account to Syncy </span>
                </div>
                <div class="modal-body">
                <p> Connect yout Facebook Account to be able to share to your Facebook pages and Facebook profile
                        </p>
                <div class="card-block">
                    <h4 class="card-title">
                        <a href="#" @click="connect()" class="btn btn-primary facebook custom">
                            Connect
                        </a>
                    </h4>
                </div>
            </div>
            </div>

        </div>
    </div>
</template>
<script>
    export default {
        name : 'connectFacebook',
        data() {
            return {
                link : null,
                name : null
            }
        },
        methods : {
          connect: function () {
              location.href= this.link;
          }
        },
        mounted () {
            this.$root.$on('connect', (obj) => {
                this.link = obj.url;
                this.name = obj.name;
            });
            jQuery('#connectFacebookAccount').modal('show');

        }
    }
</script>