<template>
  <div>
    <md-list class="list">

      <md-list-item style="border: black;" class="headerList">
        <div class="list-line header" v-if="!this._props.englishSwitch_Table">
          <span class="list-header">종류</span>
          <span class="list-header">대여 가능</span>
          <span class="list-header">사용 불가</span>
          <span class="list-header">고장</span>
          <span class="list-header">수리중</span>
          <span class="list-header">대여중</span>
          <span class="list-header">총 갯수</span>
        </div>
        <div class="list-line header" v-if="this._props.englishSwitch_Table">
          <span class="list-header">Product</span>
          <span class="list-header">Rentable</span>
          <span class="list-header">Unusable</span>
          <span class="list-header">Failure</span>
          <span class="list-header">Repair</span>
          <span class="list-header">Renting</span>
          <span class="list-header">Total</span>
        </div>
      </md-list-item>
      <md-content class="md-scrollbar tableScrollDiv">
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
            <md-table-row v-if="!englishSwitch">
              <md-table-head>바코드ID</md-table-head>
              <md-table-head>물품 인덱스</md-table-head>
              <md-table-head>이름</md-table-head>
              <md-table-head>소속</md-table-head>
              <md-table-head>구매 일자</md-table-head>
            </md-table-row>

            <md-table-row v-if="englishSwitch">
              <md-table-head>Barcode</md-table-head>
              <md-table-head>Index</md-table-head>
              <md-table-head>Product</md-table-head>
              <md-table-head>Attached</md-table-head>
              <md-table-head>Generation Date</md-table-head>
            </md-table-row>

            <md-table-row v-for="product in item.products" @click="toggleDialog(product)">
              <md-table-cell>{{ product.product_barcode }}</md-table-cell>
              <md-table-cell>{{ product.product_index }}</md-table-cell>
              <md-table-cell>{{ product.product_name }}</md-table-cell>
              <md-table-cell>{{ product.product_owner_name }}</md-table-cell>
              <md-table-cell>{{ product.product_created }}</md-table-cell>
            </md-table-row>

          </md-table>
        </md-list-item>
      </md-content>
    </md-list>



    <md-dialog :md-active.sync="showDialog">
      <md-dialog-title v-if="!englishSwitch">제품 상세</md-dialog-title>
      <md-dialog-title v-if="englishSwitch">Product Detail</md-dialog-title>
      <md-dialog-content v-if="!englishSwitch">
        <div>바코드 ID : {{ dialogInfo.product_barcode }}</div>
        <div>이름 : {{ dialogInfo.product_name }}</div>
        <div>소속 : {{ dialogInfo.product_owner_name }}</div>
        <div>구매 일자 : {{ dialogInfo.product_created }}</div>
        <div>로그</div>
        <md-content class="md-scrollbar logScrollDiv">
          <md-table :table-header-color="tableHeaderColor">
            <md-table-row>
              <md-table-head>생성 시간</md-table-head>
              <md-table-head>로그 타입</md-table-head>
              <md-table-head>생성 유저 ID</md-table-head>
              <md-table-head>생성 유저 이름</md-table-head>
            </md-table-row>

            <md-table-row v-for="log in logList">
              <md-table-cell>{{ log.time }}</md-table-cell>
              <md-table-cell>{{ log.type }}</md-table-cell>
              <md-table-cell>{{ log.userID }}</md-table-cell>
              <md-table-cell>{{ log.userName }}</md-table-cell>
            </md-table-row>

            <md-table-row v-if="logList.length == 0">
              <md-table-cell>로그가 없습니다.</md-table-cell>
            </md-table-row>

          </md-table>
        </md-content>

      </md-dialog-content>

      <md-dialog-content v-if="englishSwitch">
        <div>Barcode : {{ dialogInfo.product_barcode }}</div>
        <div>Product : {{ dialogInfo.product_name }}</div>
        <div>Attached : {{ dialogInfo.product_owner_name }}</div>
        <div>Generation Date : {{ dialogInfo.product_created }}</div>
        <div>Log</div>
        <md-content class="md-scrollbar logScrollDiv">
          <md-table :table-header-color="tableHeaderColor">
            <md-table-row>
              <md-table-head>Time</md-table-head>
              <md-table-head>Type</md-table-head>
              <md-table-head>ID</md-table-head>
              <md-table-head>User Name</md-table-head>
            </md-table-row>

            <md-table-row v-for="log in logList">
              <md-table-cell>{{ log.time }}</md-table-cell>
              <md-table-cell>{{ log.type }}</md-table-cell>
              <md-table-cell>{{ log.userID }}</md-table-cell>
              <md-table-cell>{{ log.userName }}</md-table-cell>
            </md-table-row>

            <md-table-row v-if="logList.length == 0">
              <md-table-cell>No Log</md-table-cell>
            </md-table-row>

          </md-table>
        </md-content>

      </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-primary" @click="showDialog = false">Close</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "stock-detail-table",
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
      productGroup: [],
      showDialog: false,
      dialogInfo: {},
      showTable: false,
      logList: [],
      englishSwitch: false
    };
  },
  components: {
  },
  created(){
    console.log("StockDetailTable");
    console.log(this._props);
    this.englishSwitch = this._props.englishSwitch_Table;
    this.exportData();
  },
  methods:{
    exportData: function(){
      var productParams = new URLSearchParams();
      var vue = this;

      productParams.append('session', vue.getCookie("session"));

      axios.post('https://api.devx.kr/GotGan/v1/product_overview.php', productParams)
      .then(function(response) {
        for(var x = 0; x < Object.keys(response.data.groups).length; x++){
          vue.productGroup.push(response.data.groups[x]);
          vue.productGroup[x].products = [];
          vue.productGroup[x].showTable = false;
        }
        axios.post('https://api.devx.kr/GotGan/v1/product_list.php', productParams)
        .then(function(response) {
          response.data.products.forEach(function(e){
            vue.productGroup.forEach(function(f){
              if(f.group_index == e.product_group_index){
                f.products.push(e);
              }
            });
          });
          console.log(vue.productGroup);
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

      var logParams = new URLSearchParams();
      var vue = this;
      logParams.append("session", this.getCookie("session"));
      logParams.append("log_product", product.product_index);

      axios.post('https://api.devx.kr/GotGan/v1/log_list.php', logParams)
      .then(function(response) {
        vue.logList = [];
        for(var i in response.data.logs){
          var logType = response.data.types[response.data.logs[i].log_type - 1].type_name;
          var obj = {
            time: response.data.logs[i].log_time,
            type: logType,
            userName: response.data.logs[i].log_user_name,
            userID: response.data.logs[i].log_user_id
          }
          vue.logList.push(obj);
        }
        console.log(vue.logList);
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

<style>
.list {
  display: block;

}

.header {
  height: 32px!important;
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
  height: 48px;
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

.tableScrollDiv > .md-list-item > .md-list-item-container > .md-list-item-content,
.headerList > .md-list-item-container > .md-list-item-content  {
  min-height: 1rem!important;
  display: block!important;
}

.md-disabled {
  padding: 0!important;

}

.list-line > .list-item {
  height: fit-content;
  padding: 1rem 0;
}

.tableScrollDiv{
  max-height: 30rem;
  overflow: auto;
}

.logScrollDiv {
  max-height: 20rem;
  overflow: auto;
}
</style>
