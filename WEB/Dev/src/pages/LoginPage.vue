<template>
  <form novalidate class="md-layout" @submit.prevent="validateUser">
    <md-card class="md-layout-item md-size-50 md-small-size-100">
      <md-card-content>
        <div class="md-layout md-gutter">
          <div class="md-layout-item md-small-size-100">
            <md-field :class="messageClass" :md-counter="false">
              <label for="id">ID</label>
              <md-input name="id" v-model="userID" maxlength="20" required/>
              <span class="md-error">There is an error</span>
            </md-field>
            <md-field :class="messageClass" :md-counter="false">
              <label for="pw">PW</label>
              <md-input name="pw" v-model="userPW" type="password"  maxlength="20" required/>
              <span class="md-error">There is an error</span>
            </md-field>

            <md-button class="md-raised" v-on:click="signIn" :to="link">Sign in</md-button>

          </div>
        </div>
      </md-card-content>
    </md-card>
  </form>
</template>

<script>
import router from '../main.js'
import axios from 'axios';
var params = new URLSearchParams();

var exportData = function(params, vueObj){
  axios.post('https://api.devx.kr/GotGan/v1/login.php', params)
  .then(function(response) {
    console.log(response.data);
    vueObj.$emit("child",response.data);
    router.push("/stockdashboard");
  })
  .catch(function(error) {
    console.log(error);
  });
};

export default {
  props: {
    _userInfo: Object
  },
  data(){
    return{
      userID: "",
      userPW: "",
      link: "",
      hasMessages: false
    }
  },
  methods: {
    signIn : function(){
      if(this.userID == "" || this.userPW == ""){
        this.hasMessages = true;
      }else{
        params.append('user_id', this.userID);
        params.append('user_pw', this.userPW);
        exportData(params, this);
      }
    }
  },
  computed: {
    messageClass () {
      return {
        'md-invalid': this.hasMessages
      }
    }
  },
  updated() {
    if(this.userID != ""){
      this.hasMessages = false;
    }
  }
};

</script>
