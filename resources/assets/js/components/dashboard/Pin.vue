<template  lang="html">
     <div class="tr" method="post" :id="'form_'+pin.pin_id" action="/share" >
        <span class="td">
            <router-link :to="'/pin/' + pin.pin_id">
                <img class="thumbnail" width="60" height="60" v-bind:src="pin.thumbnail" >
            </router-link>
        </span>
        <span class="td">
            <textarea class="form-control caption-textarea" :name="'pins['+pin.pin_id+'][caption]'" :id="'caption_'+pin.pin_id" rows="2" cols="100"> {{ pin.caption }}</textarea>
        </span>

        <span class="td">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Share
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <router-link v-on:click.native="submitForm(pin.pin_id)" type="button" class="btn" to="/schedule"> Facebook Page </router-link>
              <router-link v-on:click.native="timelinePost()" type="button" class="btn" :to="'/share/' + pin.pin_id"> Facebook Account </router-link>
              <router-link v-on:click.native="timelinePost()" to="/instagram/share" type="button" class="btn" >Instagram Account</router-link>
          </div>
        </div>

        </span>
    </div>
  </template>

<script>
    import bus from './../Bus.vue';
    export default {
        data() {
            return {

                formInputs: {},
                formErrors: {},
                facebook: {},
                instagram: {},
                scheduled_at: {},
                startDate: '',
                caption : {},
            }
        },
        methods: {
            submitForm(pinID) {
                bus.caption = this.pin.caption;
                bus.pin_id = pinID;
                bus.pin = this.pin;
            },
            timelinePost() {
                bus.pin = this.pin;
            },
            setDate(input) {
                let pinID       =  input.data('pin-id');
                let dateTime    =  input.val();
                Vue.set(this.scheduled_at, pinID, dateTime);
            }
        },
        props: ['pin','errors', 'csrftoken', ''],
        mounted() {
        }
    }
</script>

<style lang="css">
  .thumbnail {
    margin-bottom : 0;
  }
  .caption-textarea {
    height: 60px !important;
  }
</style>