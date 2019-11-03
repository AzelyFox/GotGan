<template>
  <div>
    <md-table v-model="users" :table-header-color="tableHeaderColor">
      <md-table-row>
        <md-table-head>종류</md-table-head>
        <md-table-head>대여 가능</md-table-head>
        <md-table-head>사용 불가</md-table-head>
        <md-table-head>고장</md-table-head>
        <md-table-head>수리중</md-table-head>
        <md-table-head>대여중</md-table-head>
        <md-table-head>총 갯수</md-table-head>
      </md-table-row>

      <md-table-row slot="md-table-row"  v-for="item in productGroup">
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
  name: "ordered-table",
  props: {
    tableHeaderColor: {
      type: String,
      default: ""
    },
    userInfo_Table: Object
  },
  data() {
    return {
      productGroup: [],
      errors: [],
      selected: [],
      users: [
        // DB에서 받아오는 대여 현황 Product_List
        {
          id: 1,
          group: "3D 프린터",
          state_Store: 80,
          state_Rent: 10,
          state_Refair: 10,
          amount: 100
        },
        {
          id: 2,
          group: "공구",
          state_Store: 5,
          state_Rent: 20,
          state_Refair: 15,
          amount: 40
        }
      ]
    };
  },
  created(){
    params.append('session', this._props.userInfo_Table.session);
    this.exportData(params);
  },
  methods:{
    exportData: function(){
      var vue = this;
      axios.post('https://devx.kr/Apps/GotGan/product_overview.php', params)
      .then(function(response) {
        console.log(response.data);
        for(var x = 1; x <= Object.keys(response.data.groups).length; x++){
          vue.productGroup.push(response.data.groups[x]);

          // 초기화
          if(vue.productGroup[x-1].group_count_available == null){
            vue.productGroup[x-1].group_count_available = 0;
          }

          if(vue.productGroup[x-1].group_count_unavailable == null){
            vue.productGroup[x-1].group_count_unavailable = 0;
          }

          if(vue.productGroup[x-1].group_count_broken == null){
            vue.productGroup[x-1].group_count_broken = 0;
          }

          if(vue.productGroup[x-1].group_count_repair == null){
            vue.productGroup[x-1].group_count_repair = 0;
          }

          if(vue.productGroup[x-1].group_count_rent == null){
            vue.productGroup[x-1].group_count_rent = 0;
          }

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
