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
<p>start</p>
<input v-model="message" placeholder="여기를 수정해보세요">
<p>메시지: {{ message }}</p>
<div class="info">
  <div class="title white-primary-hover" role="lin">{{ test }}</div>

</div>
<p>end</p>
  </div>
</template>

<script>
import axios from 'axios';
var params = new URLSearchParams();

var exportData = function(params){
  axios.post('https://devx.kr/Apps/GotGan/product_list.php', params)
  .then(function(response) {
    console.log(response);
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
    },
    userInfo_Table: Object
  },
  data() {
    return {
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
  created(){
    params.append('session', this._props.userInfo_Table.session);
    exportData(params);
    console.log(this._props);
  }
};
</script>
