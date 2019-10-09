<template lang="html">
    <div id="profile" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                     <div class="modal-body">
                        <div class="row" v-show="loaded" >
                             <div class="profile">
                                <div class="col-sm-12">
                                    <div class="col-xs-12 col-sm-12">
                                        <img class="profile-img" :src="imgSrc" />
                                        <h2> {{ name }} </h2>
                                        <p> {{ email }} </p>
                                    </div>
                                </div>
                                <div class="col-xs-12 divider text-center">
                                    <div class="col-xs-12 col-sm-6 emphasis">
                                        <h2><strong> {{ pinsCount }} </strong></h2>
                                        <p><small>Pins</small></p>
                                        <button @click="openProfile('')" class="btn btn-success btn-block"><span class="fa fa-plus-circle"></span> View profile </button>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 emphasis">
                                        <h2><strong> {{ boardsCount }}</strong></h2>
                                        <p><small>Boards</small></p>
                                        <button @click="openProfile('/boards')"  class="btn btn-info btn-block"><span class="fa fa-user"></span> View Boards </button>
                                    </div>
                                </div>
                             </div>
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
        name : 'profile',
        data() {
          return {
              user : null,
              loaded : false,
              name : null,
              email : null,
              imgSrc : null,
              pinsCount : 0,
              boardsCount : 0,
              url : null,
          }
        },
        methods : {
            cancel() {
                this.$parent.cancel(jQuery('#profile'));
            },
            openProfile(uri) {
                window.open(this.url + uri,'_blank');
            }
        },
        mounted() {
            jQuery('#profile').modal('show');
            //get user details
            axios.get ('/user/details/get')
                .then(response => {
                   this.user =  response.data.user;
                   this.name = this.user.name;
                   this.email = this.user.email;
                   this.imgSrc = this.user.img;
                   this.url = this.user.pinterest_url;
                   this.loaded = true;
                   this.pinsCount = response.data.MyPinsCount;
                   this.boardsCount = Object.keys(response.data.boards).length;
                });
        }
    }
</script>
<style>
    .profile-img {
        border-radius: 100%;
    }
</style>