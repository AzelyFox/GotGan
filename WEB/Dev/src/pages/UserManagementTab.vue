<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card>
          <md-card-header data-background-color="green">
            <h4 class="title">유저 관리</h4>
            <p class="category">유저 관리 관련 카드</p>
          </md-card-header>
          <md-card-content>
            <user-table  :userInfo_Table="userInfo_Tab"></user-table>
          </md-card-content>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
import {
  UserTable
} from "@/components";

export default {
  props: {
    userInfo_Tab: Object
  },
  components: {
    UserTable
  },
  created() {
    console.log("UserManagementTab");
    console.log(this._props.userInfo_Tab);
  }
};
</script>
