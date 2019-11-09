<template>
      <form novalidate class="md-layout" @submit.prevent="validateUser">
        <div class="md-layout-item md-size-35"></div>
        <md-card class="md-layout-item md-size-30 md-small-size-100 md-gutter card">
          <md-card-header>
            <span class="md-title">GotGan</span>
          </md-card-header>
          <md-card-content>
            <div class="md-layout md-gutter">
              <div class="md-layout-item md-small-size-100 cardDiv">
                <md-field :class="messageClass" :md-counter="false">
                  <label for="userID">ID</label>
                  <md-input name="userID" v-model="user_ID" maxlength="20" required/>
                  <span class="md-error">There is an error</span>
                </md-field>

                <md-field :class="messageClass" :md-counter="false">

                  <label for="userPW">Password</label>
                  <md-input name="userPW" v-model="user_Password" type="password"  maxlength="20" required/>
                  <span class="md-error">There is an error</span>
                </md-field>

                <transition-group name="slide-fade">
                  <div v-bind:key="showCard">
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="userLevel">권한</label>
                      <md-input name="userLevel" v-model="user_Level" maxlength="20" required/>
                      <span class="md-error">There is an error</span>
                    </md-field>
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="userName">이름</label>
                      <md-input name="userName" v-model="user_Name" maxlength="20" required/>
                      <span class="md-error">There is an error</span>
                    </md-field>
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="userGroup">유저그룹</label>
                      <md-input name="userGroup" v-model="user_Group" maxlength="20"/>
                    </md-field>
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="userSID">학번</label>
                      <md-input name="userSID" v-model="user_SID" maxlength="20"/>
                    </md-field>
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="userEmail">이메일</label>
                      <md-input name="userEmail" v-model="user_Email" maxlength="20"/>
                    </md-field>
                    <md-field :class="messageClass" :md-counter="false" v-if="showCard">
                      <label for="user_Phone">전화번호</label>
                      <md-input name="user_Phone" v-model="user_Phone" maxlength="20"/>
                    </md-field>
                  </div>
                </transition-group>
              </div>
            </div>
          </md-card-content>
          <md-card-actions>
            <md-button class="md-raised" v-on:click="signIn" :to="link" v-if="showCard == 0">Sign in</md-button>
            <md-button v-on:click="showCard = 1" v-if="showCard == 0">Sign up</md-button>
            <md-button v-on:click="showCard = 0" v-if="showCard == 1">Register</md-button>
          </md-card-actions>
        </md-card>
        <div class="md-layout-item md-size-35"></div>
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
      user_ID: "",
      user_Password: "",
      link: "",
      hasMessages: false,
      showCard: 0,
      user_Level: "",
      user_Name: "",
      user_Group: "",
      user_SID: "",
      user_Email: "",
      user_Phone: ""
    }
  },
  methods: {
    signIn : function(){
      if(this.userID == "" || this.userPW == ""){
        this.hasMessages = true;
      }else{
        params.append('user_id', this.user_ID);
        params.append('user_pw', this.user_Password);
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

<style>
  .card {
    margin-top: 12rem!important;
  }

  .cardDiv {
    transition-property: all;
    transition-duration: 1s;
  }

  .slide-fade-enter-active {
    transition: all .3s ease;
  }
  .slide-fade-leave-active {
    transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
  }
  .slide-fade-enter, .slide-fade-leave-to
  /* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
  }
</style>
