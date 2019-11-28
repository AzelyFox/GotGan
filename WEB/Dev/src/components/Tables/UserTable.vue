<template>
  <div>
    <md-table>
      <md-table-row v-if="!this._props.englishSwitch_Table">
        <md-table-head>유저 인덱스</md-table-head>
        <md-table-head>ID</md-table-head>
        <md-table-head>이름</md-table-head>
        <md-table-head>소속</md-table-head>
        <md-table-head>권한</md-table-head>
        <md-table-head>학번</md-table-head>
        <md-table-head>가입일</md-table-head>
    </md-table-row>

    <md-table-row v-if="this._props.englishSwitch_Table">
      <md-table-head>User Index</md-table-head>
      <md-table-head>ID</md-table-head>
      <md-table-head>Name</md-table-head>
      <md-table-head>Group</md-table-head>
      <md-table-head>Authority</md-table-head>
      <md-table-head>StudentID</md-table-head>
      <md-table-head>Registration Date</md-table-head>
  </md-table-row>

    <md-table-row v-for="item in userList">
      <md-table-cell>{{ item.user_index }}</md-table-cell>
      <md-table-cell>{{ item.user_id }}</md-table-cell>
      <md-table-cell>{{ item.user_name}}</md-table-cell>
      <md-table-cell>{{ item.user_group_name}}</md-table-cell>
      <md-table-cell>{{ item.user_level}}</md-table-cell>
      <md-table-cell>{{ item.user_sid}}</md-table-cell>
      <md-table-cell>{{ item.user_created}}</md-table-cell>
    </md-table-row>
  </md-table>
</div>
</template>

<script>
import axios from 'axios';

export default {
  name: "stock-table",
  props: {
    userInfo_Table: Object,
    englishSwitch_Table: Boolean
  },
  data() {
    return {
      userList: []
    };
  },
  created(){
    console.log("UserTable");
    console.log(this._props.userInfo_Table);

    this.exportData();
  },
  methods:{
    exportData: function(){
      var userParams = new URLSearchParams();
      var vue = this;

      userParams.append('session', vue.getCookie("session"));

      axios.post('https://api.devx.kr/GotGan/v1/user_list.php', userParams)
      .then(function(response) {
        console.log(response.data);
        for(var x = 0; x < Object.keys(response.data.users).length; x++){
          vue.userList.push(response.data.users[x]);
        }
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    }
  }
};
</script>
