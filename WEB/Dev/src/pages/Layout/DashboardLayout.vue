<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">

    <side-bar>
      <mobile-menu slot="content"></mobile-menu>
      <sidebar-link to="/stockDashboard">
        <md-icon>view_module</md-icon>
        <p>재고 대시보드</p>
      </sidebar-link>
      <sidebar-link to="/stockDetail">
        <md-icon>content_paste</md-icon>
        <p>재고 상세</p>
      </sidebar-link>
      <sidebar-link to="/rentDashboard">
        <md-icon>view_quilt</md-icon>
        <p>반출입 대시보드</p>
      </sidebar-link>
      <sidebar-link to="/userManagement">
        <md-icon>person</md-icon>
        <p>유저 관리</p>
      </sidebar-link>
      <sidebar-link to="/setting">
        <md-icon>settings_applications</md-icon>
        <p>설정</p>
      </sidebar-link>
    </side-bar>

    <div class="main-panel">
      <top-navbar></top-navbar>

      <dashboard-content :userInfo_Content="userInfo"> </dashboard-content>

      <content-footer v-if="!$route.meta.hideFooter"></content-footer>
    </div>
  </div>
</template>
<style lang="scss"></style>
<script>
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
      userInfo: {}
    }
  },
  created(){
    this.userInfo = this._props._userInfo;
    console.log(this.userInfo);
  }
};
</script>
