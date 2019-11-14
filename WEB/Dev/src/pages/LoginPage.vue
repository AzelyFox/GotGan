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
                    <md-field :md-counter="false" v-if="showCard">
                      <label for="userName">이름</label>
                      <md-input name="userName" v-model="user_Name" maxlength="20" required/>
                      <span class="md-error">There is an error</span>
                    </md-field>

                    <md-field :md-counter="false" v-if="showCard">
                      <label for="userEmail">이메일</label>
                      <md-input name="userEmail" v-model="user_Email" maxlength="20"/>
                    </md-field>

                    <md-field :md-counter="false" v-if="showCard">
                      <label for="user_Phone">전화번호</label>
                      <md-input name="user_Phone" v-model="user_Phone" maxlength="20"/>
                    </md-field>

                    <md-field :md-counter="false" v-if="showCard">
                      <label for="userGroup">유저그룹</label>
                      <md-input name="userGroup" v-model="user_Group" maxlength="20"/>
                    </md-field>

                    <md-field :md-counter="false" v-if="showCard">
                      <label for="userSID">학번</label>
                      <md-input name="userSID" v-model="user_SID" maxlength="20"/>
                    </md-field>
                  </div>

                  <!--
                  user_id (string) [필수 인자]
                  유저의 아이디를 의미한다.

                  user_pw (string) [필수 인자]
                  유저의 비밀번호를 의미한다.
                  서버에서 bcrypt 알고리즘을 통해 암호화하여 저장한다.

                  user_level (int) [필수 인자]
                  유저의 계정 등급을 의미한다.
                  0 : 일반 사용자
                  1 : 관리자
                  2 : 최고 관리자

                  user_name (string) [필수 인자]
                  유저명을 의미한다.

                  user_group (int) [선택 인자]
                  유저가 속할 그룹을 의미한다.
                  전달받은 인자가 없을 경우 0번 그룹에 기본으로 속한다.

                  user_sid (string) [선택 인자]
                  유저의 학번을 의미한다.

                  user_email (string) [선택 인자]
                  유저의 이메일을 의미한다.

                  user_phone (string) [선택 인자]
                  유저의 휴대기기 번호를 의미한다.-->
                </transition-group>
              </div>
            </div>
          </md-card-content>
          <md-card-actions>
            <md-button class="md-raised" v-on:click="signIn" :to="link" v-if="showCard == 0">Sign in</md-button>
            <md-button v-on:click="showCard = 1" v-if="showCard == 0">Sign up</md-button>
            <md-button v-on:click="showCard = 0" v-if="showCard == 1">Cancle</md-button>
            <md-button v-on:click="signUp" v-if="showCard == 1">Register</md-button>
          </md-card-actions>
        </md-card>
        <div class="md-layout-item md-size-35"></div>
      </form>
</template>

<script>
import router from '../main.js'
import axios from 'axios';

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
      user_Level: 0,
      user_Name: "",
      user_Group: 0,
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
        var signInParams = new URLSearchParams();
        var vue = this;
        signInParams.append('user_id', this.user_ID);
        signInParams.append('user_pw', this.user_Password);

        axios.post('https://api.devx.kr/GotGan/v1/login.php', signInParams)
        .then(function(response) {
          vue.$emit("child",response.data);
          if(response.data.result == 0){
            //로그인 성공
            response.data.user_level == 2 ? router.push("/stockdashboard") : router.push("/user");
          }else{
            // 로그인 실패
            alert("ERROR");
          }

        })
        .catch(function(error) {
          console.log(error);
        });
      }
    },
    signUp : function(){
      var signUpParams = new URLSearchParams();
      signUpParams.append('user_id', this.user_ID);
      signUpParams.append('user_pw', this.user_Password);
      signUpParams.append('user_level', this.user_Level);
      signUpParams.append('user_name', this.user_Name);
      signUpParams.append('user_email', this.user_Email);
      signUpParams.append('user_phone', this.user_Phone);
      signUpParams.append('user_group', this.user_Group);
      signUpParams.append('user_sid', this.user_SID);

      axios.post('https://api.devx.kr/GotGan/v1/user_add.php', signUpParams)
      .then(function(response) {
        console.log(response.data);
      })
      .catch(function(error) {
        console.log(error);
      });
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
