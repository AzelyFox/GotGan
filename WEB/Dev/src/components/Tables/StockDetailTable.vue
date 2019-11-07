<template>
  <div>
    <md-list>

      <md-list-item style="border: black;">
        <div  class="list-line">
          <span class="list-header">이름</span>
          <span class="list-header">상태 코드</span>
          <span class="list-header">소유 유저 그룹 인덱스</span>
          <span class="list-header">그룹명</span>
          <span class="list-header">대여인덱스</span>
          <span class="list-header">바코드번호</span>
          <span class="list-header">처음시간</span>
        </div>
      </md-list-item>

        <md-list-item v-for="item in productGroup">
          <div class="list-line" @click="toggle(item)">
            <span class="list-item">{{ item.group_name }}</span>
            <span class="list-item">{{ item.group_count_available - item.group_count_rent }}</span>
            <span class="list-item">{{ item.group_count_unavailable}}</span>
            <span class="list-item">{{ item.group_count_broken}}</span>
            <span class="list-item">{{ item.group_count_repair}}</span>
            <span class="list-item">{{ item.group_count_rent}}</span>
            <span class="list-item">{{ item.group_count_available + item.group_count_unavailable + item.group_count_broken + item.group_count_repair}}</span>
          </div>
          <md-table v-show="item.showTable">
            <md-table-row>
              <md-table-head md-numeric>바코드ID</md-table-head>
              <md-table-head>이름</md-table-head>
              <md-table-head>소속</md-table-head>
              <md-table-head>구매 일자</md-table-head>
            </md-table-row>

            <md-table-row v-for="product in item.products" @click="toggleDialog(product)">
              <md-table-cell md-numeric>{{ product.product_barcode }}</md-table-cell>
              <md-table-cell>{{ product.product_name }}</md-table-cell>
              <md-table-cell>{{ product.product_owner_name }}</md-table-cell>
              <md-table-cell>{{ product.product_created }}</md-table-cell>
            </md-table-row>
          </md-table>
        </md-list-item>
      </md-list>



    <md-dialog :md-active.sync="showDialog">
      <md-dialog-title>제품 상세</md-dialog-title>
      <md-dialog-content>
        <md-table :table-header-color="tableHeaderColor">
          <md-table-row>
            <md-table-head md-numeric>바코드ID</md-table-head>
            <md-table-head>이름</md-table-head>
            <md-table-head>소속</md-table-head>
            <md-table-head>구매 일자</md-table-head>
          </md-table-row>

          <md-table-row>
            <md-table-cell md-numeric>{{ dialogInfo.product_barcode }}</md-table-cell>
            <md-table-cell>{{ dialogInfo.product_name }}</md-table-cell>
            <md-table-cell>{{ dialogInfo.product_owner_name }}</md-table-cell>
            <md-table-cell>{{ dialogInfo.product_created }}</md-table-cell>
          </md-table-row>
          <!--
          product_barcode: 0
          product_created: "2019-11-05 17:59:05"
          product_group_index: 1
          product_group_name: "3D 프린터"
          product_group_priority: 0
          product_index: 32
          product_name: "3D 프린터 (대형)"
          product_owner_index: 1
          product_owner_name: "총무부"
          product_rent_index: 0
          product_status: 0
        -->
        </md-table>
      </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-primary" @click="showDialog = false">Close</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import axios from 'axios';

var params = new URLSearchParams();
export default {
  name: "stock-detail-table",
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
      showDialog: false,
      dialogInfo: {},
      showTable: false
    };
  },
  components: {
  },
  created(){
    console.log("StockDetailTable");
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
          vue.productGroup[x].products = [];
          vue.productGroup[x].showTable = false;
        }
        axios.post('https://api.devx.kr/GotGan/v1/product_list.php', params)
        .then(function(response) {
          response.data.products.forEach(function(e){
            vue.productGroup.forEach(function(f){
              if(f.group_index == e.product_group_index){
                f.products.push(e);
              }
            });
          });

          console.log(vue);
        })
        .catch(function(error) {
          console.log(error);
        });
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    toggle: function(group){
      group.showTable ? group.showTable=false : group.showTable=true;
      this.$forceUpdate();
    },
    toggleDialog: function(product){
      this.showDialog = true;
      this.dialogInfo = product;
    }
  }
};
</script>

<style>
  .list {
    display: block;

  }

  .list-header, .list-item{
      padding-left: 8px;
      font-weight: 300;
    position: inherit!important;
  }

  .list-header {
    font-size: 1.0625rem;
    color: rgba(0, 0, 0, 0.54);
    color: var(--md-theme-default-text-accent-on-background, rgba(0, 0, 0, 0.54));
  }

  .list-line {
        font-size: 14px;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
    border-bottom: thin solid #f0f0f0;
        padding-bottom: 4px;
        transition: .3s cubic-bezier(.4,0,.2,1);
    transition-property: background-color,font-weight;
    will-change: background-color,font-weight;
  }

  .list-line:hover {
    background-color: #f0f0f0;
  }

  .md-list-item-content {
    display: block!important;
  }

  .md-disabled {
    padding: 0!important;

  }


</style>
