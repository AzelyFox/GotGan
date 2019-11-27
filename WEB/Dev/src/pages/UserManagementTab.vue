<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card>
          <md-card-header data-background-color="green">
            <h4 class="title" v-if="!this._props.englishSwitch_Tab">유저 관리</h4>
            <p class="category" v-if="!this._props.englishSwitch_Tab">유저 관리 관련 카드</p>
              <h4 class="title" v-if="this._props.englishSwitch_Tab">User Management</h4>
              <p class="category" v-if="this._props.englishSwitch_Tab">Card about user management</p>
          </md-card-header>
          <md-card-content>
            <user-table  :userInfo_Table="userInfo_Tab" :englishSwitch_Table="englishSwitch_Tab"></user-table>
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
    userInfo_Tab: Object,
    englishSwitch_Tab: Boolean
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
