<template>
  <div>
    <md-table v-model="users" :table-header-color="tableHeaderColor">
      <md-table-row slot="md-table-row" slot-scope="{ item }">
        <md-table-cell md-label="ID">{{ item.id }}</md-table-cell>
        <md-table-cell md-label="종류">{{ item.group }}</md-table-cell>
        <md-table-cell md-label="보관중">{{ item.state_Store }}</md-table-cell>
        <md-table-cell md-label="대여중">{{ item.state_Rent}}</md-table-cell>
        <md-table-cell md-label="수리중">{{ item.state_Refair }}</md-table-cell>
        <md-table-cell md-label="총 수량">{{ item.amount }}</md-table-cell>
      </md-table-row>
    </md-table>

  </li>
  <!-- 트리 뷰 테스트 -->
  <ul>
    <test-two class="TestTwo" :model="treeData"></test-two>
  </ul>
  </div>
</template>

<script>
import axios from 'axios';
import TestTwo from '../TestTwo.vue';
// demo data
var Tdata = {
  name: 'My Tree',
  children: [
    { name: 'hello' },
    { name: 'wat' },
    {
      name: 'child folder',
      children: [
        {
          name: 'child folder',
          children: [
            { name: 'hello' },
            { name: 'wat' }
          ]
        },
        { name: 'hello' },
        { name: 'wat' },
        {
          name: 'child folder',
          children: [
            { name: 'hello' },
            { name: 'wat' }
          ]
        }
      ]
    }
  ]
}

var params = new URLSearchParams();
params.append('value', 'hi');

var t1;

var exportData = function(){
  axios.post('https://api.devx.kr/GotGan/v1/product_overview.php', params)
  .then(function(response) {
    console.log(response);
    t1 = response.data;
    //console.log(this);
  })
  .catch(function(error) {
    console.log(error);
  });
};

export default {
  name: "ordered-table",
  props: {
    tableHeaderColor: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      treeData: Tdata,
      message: [],
      test: [],
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
  components: {
    TestTwo
  },
  created(){
    exportData();
  },
  updated() {
    console.log("UPDATE!!");
    console.log(this);
    this.test = t1;
  }
};
</script>
