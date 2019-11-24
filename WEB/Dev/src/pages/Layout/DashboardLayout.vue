<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">

    <side-bar v-if="user_Level == 2">
      <mobile-menu slot="content"></mobile-menu>
      <sidebar-link to="/admin/stockDashboard">
        <md-icon>view_module</md-icon>
        <p>재고 대시보드</p>
      </sidebar-link>

      <sidebar-link to="/admin/stockDetail">
        <md-icon>content_paste</md-icon>
        <p>재고 상세</p>
      </sidebar-link>


      <sidebar-link to="/admin/rentDashboard">
        <md-icon>view_quilt</md-icon>
        <div class="md-badge-content" v-if="showNum > 0">
          <div class="md-badge md-theme-default md-position-top">
            <div>
              {{ showNum }}
            </div>
          </div>
        </div>
        <p>반출입 대시보드</p>
      </sidebar-link>

      <sidebar-link to="/admin/userManagement">
        <md-icon>person</md-icon>
        <p>유저 관리</p>
      </sidebar-link>

      <sidebar-link to="/admin/setting">
        <md-icon>settings_applications</md-icon>
        <p>설정</p>
      </sidebar-link>
    </side-bar>

    <side-bar v-if="user_Level == 0">
      <mobile-menu slot="content"></mobile-menu>
      <sidebar-link to="/user">
        <md-icon>view_module</md-icon>
        <p>사용자 페이지</p>
      </sidebar-link>
    </side-bar>

    <div class="main-panel">
      <top-navbar :userName_Top="userName"></top-navbar>

      <dashboard-content :userInfo_Content="userInfo"></dashboard-content>

      <content-footer v-if="!$route.meta.hideFooter"></content-footer>
    </div>
  </div>
</template>

<script>
import router from '../../main.js'
import axios from 'axios';

import TopNavbar from "./TopNavbar.vue";
import ContentFooter from "./ContentFooter.vue";
import DashboardContent from "./Content.vue";
import MobileMenu from "@/pages/Layout/MobileMenu.vue";

export default {
  props: {
    _userInfo: Object
  },
  components: {
    TopNavbar,
    DashboardContent,
    ContentFooter,
    MobileMenu
  },
  data(){
    return{
      userInfo: {},
      user_Level: 0,
      session: "",
      userName: "",
      rentNum: 0,
      showNum: 0
    }
  },
  created(){
    console.log("DashboardLayout");
    console.log(this._props._userInfo);
    this.session = this.getCookie("session");

    this.$EventBus.$on('updateSideBarBadge', () => {
      this.updateData();
    });

    if(Object.keys(this._props._userInfo).length == 0){
      this.login();
    }else{
      this.user_Level = this._props._userInfo.user_level;
      this.userInfo = this._props._userInfo;
      this.userName = this._props._userInfo.user_name;
    }

    this.updateData();
  },
  methods: {
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    },
    login : function(){
      var signInParams = new URLSearchParams();
      var vue = this;
      signInParams.append('session', this.session);

      axios.post('https://api.devx.kr/GotGan/v1/login.php', signInParams)
      .then(function(response) {
        if(response.data.result == 0){
          // 로그인 성공
          vue.user_Level = response.data.user_level;
          vue.userInfo = {
            error: response.data.error,
            result: response.data.result,
            session: response.data.session,
            user_block: response.data.user_block,
            user_created: response.data.user_created,
            user_email: response.data.user_email,
            user_group_index: response.data.user_group_index,
            user_group_name: response.data.user_group_name,
            user_id: response.data.user_id,
            user_index: response.data.user_index,
            user_level: response.data.user_level,
            user_name: response.data.user_name,
            user_phone: response.data.user_phone,
            user_sid: response.data.user_sid
          };
          vue.userName = response.data.user_name;
        }else{
          // 로그인 실패
          alert("다시 로그인 하시오.");
          router.push("/");
        }
      })
      .catch(function(error) {
        console.log(error);
      });

    },
    updateData: function(){
      var params = new URLSearchParams();
      var vue = this;
      params.append('session', this.session);

      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', params)
      .then(function(response) {
        vue.rentNum = 0;
        for(var i in response.data.rents){
          response.data.rents[i].rent_status == 1 ? vue.rentNum++ : 0;
        }
        vue.showNum != vue.rentNum? vue.showNum = vue.rentNum : 0;
      })
      .catch(function(error) {
        console.log(error);
      });
    }
  },
  updated() {
    this.updateData();
  }
};

</script>

<style lang="scss">
.md-badge-content {
  position: absolute!important;
  left: 2.3rem;
  bottom: 1.6rem;
}
</style>
