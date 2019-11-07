<template>
  <div>
    <md-table :table-header-color="tableHeaderColor">
      <md-table-row>
        <md-table-head>종류</md-table-head>
        <md-table-head>대여 가능</md-table-head>
        <md-table-head>사용 불가</md-table-head>
        <md-table-head>고장</md-table-head>
        <md-table-head>수리중</md-table-head>
        <md-table-head>대여중</md-table-head>
        <md-table-head>총 갯수</md-table-head>
      </md-table-row>

      <md-table-row v-for="item in productGroup">
        <md-table-cell>{{ item.group_name }}</md-table-cell>
        <md-table-cell>{{ item.group_count_available - item.group_count_rent }}</md-table-cell>
        <md-table-cell>{{ item.group_count_unavailable}}</md-table-cell>
        <md-table-cell>{{ item.group_count_broken}}</md-table-cell>
        <md-table-cell>{{ item.group_count_repair}}</md-table-cell>
        <md-table-cell>{{ item.group_count_rent}}</md-table-cell>
        <md-table-cell>{{ item.group_count_available + item.group_count_unavailable + item.group_count_broken + item.group_count_repair}}</md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
import axios from 'axios';
var params = new URLSearchParams();

export default {
  name: "stock-table",
  props: {
    tableHeaderColor: {
      type: String,
      default: ""
    },
    userInfo_Table: Object
  },
  data() {
    return {
      productGroup: []
    };
  },
  created(){
    console.log("StockTable");
    console.log(this._props);
    params.append('session', this._props.userInfo_Table.session);
    this.exportData(params);
  },
  methods:{
    exportData: function(){
      var vue = this;
      axios.post('https://api.devx.kr/GotGan/v1/product_overview.php', params)
      .then(function(response) {
        console.log(response.data);
        for(var x = 0; x < Object.keys(response.data.groups).length; x++){
          vue.productGroup.push(response.data.groups[x]);
        }


        console.log(vue.productGroup);
      })
      .catch(function(error) {
        console.log(error);
      });
    }
  }
};
</script>
