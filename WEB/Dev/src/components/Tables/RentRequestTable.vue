<template>
  <div>
    <md-table :table-header-color="tableHeaderColor">
      <md-table-row>
        <md-table-head>이름</md-table-head>
        <md-table-head>항목</md-table-head>
        <md-table-head>시작일</md-table-head>
        <md-table-head>반납 예정일</md-table-head>
        <md-table-head>처리 버튼</md-table-head>
      </md-table-row>
      <md-table-row slot="md-table-row" v-for="item in rentList" v-if="item.rent_status == 1">
        <md-table-cell>{{ item.rent_user_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_start }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_end }}</md-table-cell>
        <md-table-cell>
          <md-button class="md-raised" data-background-color="blue" @click="allowButton(item.rent_index)">허가</md-button>
          <md-button class="md-raised" data-background-color="red" @click="">거부</md-button>
        </md-table-cell>
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
    userInfo_Table: Object
  },
  data() {
    return {
      selected: [],
      rentList: []
    };
  },
  created(){
    console.log("RentRequestTable");
    console.log(this._props);
    params.append('session', this.getCookie("session"));
    this.exportData(params);
  },
  methods:{
    exportData: function(){
      var vue = this;
      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', params)
      .then(function(response) {
        console.log(response.data);
        for(var x = 0; x < Object.keys(response.data.rents).length; x++){
          vue.rentList.push(response.data.rents[x]);
        }

      })
      .catch(function(error) {
        console.log(error);
      });
    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    },
    allowButton: function(_index) {
      console.log(_index);
      var vue = this;
      var allowParams = new URLSearchParams();
      allowParams.append('session', this.getCookie("session"));
      allowParams.append('rent_index', _index);


      axios.post('https://api.devx.kr/GotGan/v1/rent_allow.php', allowParams)
      .then(function(response) {
        console.log(response.data);
        vue.$forceUpdate();
      })
      .catch(function(error) {
        console.log(error);
      });
    },

  }
};


</script>
