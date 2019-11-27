<template>
  <div>
    <md-table :table-header-color="tableHeaderColor">
      <md-table-row v-if="!this._props.englishSwitch_Table">
        <md-table-head>이름</md-table-head>
        <md-table-head>항목</md-table-head>
        <md-table-head>시작일</md-table-head>
        <md-table-head>반납예정일</md-table-head>
        <md-table-head>처리 버튼</md-table-head>
      </md-table-row>

      <md-table-row v-if="this._props.englishSwitch_Table">
        <md-table-head>Name</md-table-head>
        <md-table-head>Product</md-table-head>
        <md-table-head>Rent Start Date</md-table-head>
        <md-table-head>Rent End Date</md-table-head>
        <md-table-head>Button</md-table-head>
      </md-table-row>

      <md-table-row class="statusRow" slot="md-table-row" v-for="item in rentList" v-if="item.rent_status == 2">
        <md-table-cell>{{ item.rent_user_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_start }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_end }}</md-table-cell>
        <md-table-cell>
          <md-button class="md-raised rentButton" data-background-color="red" @click="returnButton(item)" v-if="!englishSwitch">반납 확인</md-button>
          <md-button class="md-raised rentButton" data-background-color="red" @click="returnButton(item)" v-if="englishSwitch">Return Check</md-button>
        </md-table-cell>
      </md-table-row>
      <md-table-row v-if="rentStatusNum == 0">
        <md-table-cell>대여 신청이 없습니다.</md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
import axios from 'axios';
var vueTest;
var params = new URLSearchParams();

export default {
  name: "rent-status-table",
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
      rentStatusNum: 0,
      englishSwitch: false
    };
  },
  created(){
    console.log("RentRequestTable");
    console.log(this._props);
    this.englishSwitch = this._props.englishSwitch_Table;
    var vue = this;

    this.$EventBus.$on('updateRentStatusTable', () => {
      vue.exportData(params);
    });

    this.$EventBus.$on('sendReturn', function(index) {
      vue.sendReturn(index);
    });

    params.append('session', this.getCookie("session"));
    this.exportData(params);
  },
  methods:{
    exportData: function(){
      var vue = this;
      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', params)
      .then(function(response) {
        console.log(response);
        vue.rentStatusNum = 0;
        vue.rentList = [];
        for(var x = 0; x < Object.keys(response.data.rents).length; x++){
          vue.rentList.push(response.data.rents[x]);
          response.data.rents[x].rent_status == 2 ? vue.rentStatusNum++ : 0 ;
        }
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    returnButton: function(obj){
      this.$EventBus.$emit('returnButton', obj);
    },
    sendReturn: function(index){
      var vue = this;
      var returnParams = new URLSearchParams();
      returnParams.append("session", this.getCookie("session"));
      returnParams.append("rent_index", index);

      axios.post('https://api.devx.kr/GotGan/v1/rent_return.php', returnParams)
      .then(function(response) {
        console.log(response.data);
        vue.exportData(params);
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    }
  },
  updated() {}
};


</script>
