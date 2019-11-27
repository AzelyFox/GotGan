<template>
  <div>
    <md-table :table-header-color="tableHeaderColor">
      <md-table-row v-if="!englishSwitch">
        <md-table-head>이름</md-table-head>
        <md-table-head>항목</md-table-head>
        <md-table-head>시작일</md-table-head>
        <md-table-head>처리 버튼</md-table-head>
      </md-table-row>

      <md-table-row v-if="englishSwitch">
        <md-table-head>Name</md-table-head>
        <md-table-head>Product</md-table-head>
        <md-table-head>Rent Start Date</md-table-head>
        <md-table-head>Button</md-table-head>
      </md-table-row>

      <md-table-row class="requestRow" slot="md-table-row" v-for="item in rentList" v-if="item.rent_status == 1">
        <md-table-cell>{{ item.rent_user_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_start }}</md-table-cell>
        <md-table-cell v-if="!englishSwitch">
          <md-button class="md-raised rentButton" data-background-color="blue" @click="allowButton(item)">허가</md-button>
          <md-button class="md-raised rentButton" data-background-color="red" @click="rejectButtion(item)">거부</md-button>
        </md-table-cell>
        <md-table-cell v-if="englishSwitch">
          <md-button class="md-raised rentButton" data-background-color="blue" @click="allowButton(item)">Allow</md-button>
          <md-button class="md-raised rentButton" data-background-color="red" @click="rejectButtion(item)">Reject</md-button>
        </md-table-cell>
      </md-table-row>

      <md-table-row v-if="rentRequestNum == 0">
        <md-table-cell>대여 신청이 없습니다.</md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
import axios from 'axios';

var params = new URLSearchParams();

export default {
  name: "simple-table",
  props: {
    tableHeaderColor: {
      type: String,
      default: ""
    },
    userInfo_Table: Object,
    englishSwitch_Table: Boolean
  },
  data() {
    return {
      selected: [],
      rentList: [],
      rentRequestNum : 0,
      englishSwitch: false
    };
  },
  created(){
    console.log("RentRequestTable");
    console.log(this._props);
    this.englishSwitch = this._props.englishSwitch_Table;
    var vue = this;

    params.append('session', this.getCookie("session"));
    this.exportData(params);

    this.$EventBus.$on('sendAllow', function(index) {
      vue.sendAllow(index);
    });

    this.$EventBus.$on('sendReject', function(index) {
      vue.sendReject(index);
    });
  },
  methods:{
    exportData: function(){
      var vue = this;
      vue.rentRequestNum = 0;
      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', params)
      .then(function(response) {
        console.log(response.data);
        vue.rentList = [];
        for(var x = 0; x < Object.keys(response.data.rents).length; x++){
          vue.rentList.push(response.data.rents[x]);
          response.data.rents[x].rent_status == 1 ? vue.rentRequestNum++ : 0 ;
        }
        console.log(vue.rentRequestNum);
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    },
    allowButton: function(obj) {
      this.$EventBus.$emit('allowButton', obj);
    },
    sendAllow: function(_index) {
      console.log(_index);
      var vue = this;
      var allowParams = new URLSearchParams();
      allowParams.append('session', this.getCookie("session"));
      allowParams.append('rent_index', _index);

      axios.post('https://api.devx.kr/GotGan/v1/rent_allow.php', allowParams)
      .then(function(response) {
        console.log(response.data);
        vue.exportData();
        vue.$EventBus.$emit('updateRentStatusTable');
        vue.$EventBus.$emit('updateSideBarBadge');
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    rejectButtion: function(obj){
      this.$EventBus.$emit('rejectButton', obj);
    },
    sendReject: function(_index){
    console.log(_index);
    var vue = this;
    var rejectParams = new URLSearchParams();
    rejectParams.append('session', this.getCookie("session"));
    rejectParams.append('rent_index', _index);

    axios.post('https://api.devx.kr/GotGan/v1/rent_delete.php', rejectParams)
    .then(function(response) {
      console.log(response.data);
      vue.exportData();
      vue.$EventBus.$emit('updateRentStatusTable');
      vue.$EventBus.$emit('updateSideBarBadge');
    })
    .catch(function(error) {
      console.log(error);
    });
  }
},
  updated() {
  }
};


</script>

<style>
.rentButton{
  margin: 0 0.8rem!important;
}
</style>
