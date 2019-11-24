<template>
  <div>
    <md-table :table-header-color="tableHeaderColor">
      <md-table-row>
        <md-table-head>이름</md-table-head>
        <md-table-head>항목</md-table-head>
        <md-table-head>시작일</md-table-head>
        <md-table-head>반납예정일</md-table-head>
        <md-table-head>처리 버튼</md-table-head>
      </md-table-row>
      <md-table-row slot="md-table-row" v-for="item in rentList" v-if="item.rent_status == 2">
        <md-table-cell>{{ item.rent_user_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_start }}</md-table-cell>
        <md-table-cell>{{ item.rent_time_end }}</md-table-cell>
        <md-table-cell>
          <md-button class="md-raised" data-background-color="red" @click="confirmButton(item.rent_index)">반납 확인</md-button>
        </md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
import axios from 'axios';

var params = new URLSearchParams();

export default {
  name: "rent-status-table",
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
    this.$EventBus.$on('updateRentStatusTable', () => {
      this.exportData(params);
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
        vue.rentList = [];
        for(var x = 0; x < Object.keys(response.data.rents).length; x++){
          vue.rentList.push(response.data.rents[x]);
        }
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    confirmButton: function(index){
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
  }
};


</script>
