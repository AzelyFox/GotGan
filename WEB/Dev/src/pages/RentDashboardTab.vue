<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card>
          <md-card-header data-background-color="red">
            <h4 class="title">대여 신청 현황</h4>
            <p class="category">현재 신청되어 있는 대여 정보 보여주기</p>
          </md-card-header>
          <md-card-content>
            <rent-request-table table-header-color="red"  :userInfo_Table="userInfo_Tab"></rent-request-table>
          </md-card-content>
        </md-card>

        <md-card>
          <md-card-header data-background-color="red">
            <h4 class="title">반출 현황</h4>
            <p class="category">현재 반출되어 있는 재고 보여주기</p>
          </md-card-header>
          <md-card-content>
            <rent-status-table table-header-color="red"  :userInfo_Table="userInfo_Tab"></rent-status-table>
          </md-card-content>
        </md-card>
      </div>
    </div>

    <md-dialog :md-active.sync="showAllowDialog">
      <md-dialog-title>대여 허가</md-dialog-title>

      <md-dialog-content>
        <p>이름 : {{ dialogInfo.rent_user_name }}</p>
        <p>대여 품목 : {{ dialogInfo.rent_product_name }}</p>
        <p>대여 시작일 : {{ dialogInfo.rent_time_start }}</p>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="sendAllowButton">허가</md-button>
        <md-button class="md-primary" @click="showAllowDialog = false">취소</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-dialog :md-active.sync="showRejectDialog">
      <md-dialog-title>대여 거부</md-dialog-title>

      <md-dialog-content>
        <p>이름 : {{ dialogInfo.rent_user_name }}</p>
        <p>대여 품목 : {{ dialogInfo.rent_product_name }}</p>
        <p>대여 시작일 : {{ dialogInfo.rent_time_start }}</p>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="sendRejectButton">거부</md-button>
        <md-button class="md-primary" @click="showRejectDialog = false">취소</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-dialog :md-active.sync="showReturnDialog">
      <md-dialog-title>반납 확인</md-dialog-title>

      <md-dialog-content>
        <p>이름 : {{ dialogInfo.rent_user_name }}</p>
        <p>대여 품목 : {{ dialogInfo.rent_product_name }}</p>
        <p>대여 시작일 : {{ dialogInfo.rent_time_start }}</p>
        <p>대여 종료일 : {{ dialogInfo.rent_time_end }}</p>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="sendReturnButton">확인</md-button>
        <md-button class="md-primary" @click="showReturnDialog = false">취소</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import {
  RentRequestTable,
  RentStatusTable
} from "@/components";

export default {
  created() {
    var vue = this;

    this.$EventBus.$on('allowButton', function(params) {
      vue.showAllow(params);
    });

    this.$EventBus.$on('rejectButton', function(params) {
      vue.showReject(params);
    });

    this.$EventBus.$on('returnButton', function(params) {
      vue.showReturn(params);
    });
  },
  props: {
    userInfo_Tab: Object
  },
  data(){
    return{
      showAllowDialog: false,
      showRejectDialog: false,
      showReturnDialog: false,
      dialogInfo: {}
    }
  },
  components: {
    RentRequestTable,
    RentStatusTable
  },
  methods: {
    showAllow: function(obj){
      console.log(obj);
      this.dialogInfo = obj;
      this.showAllowDialog = true;
    },
    showReject: function(obj){
      console.log(obj);
      this.dialogInfo = obj;
      this.showRejectDialog = true;
    },
    showReturn: function(obj){
      console.log(obj);
      this.dialogInfo = obj;
      this.showReturnDialog = true;
    },
    sendAllowButton: function(){
      console.log(this.dialogInfo);
      var vue = this;
      this.showAllowDialog = false;
      this.$EventBus.$emit('sendAllow', vue.dialogInfo.rent_index);
    },
    sendRejectButton: function(){
      var vue = this;
      this.showRejectDialog = false;
      this.$EventBus.$emit('sendReject', vue.dialogInfo.rent_index);
    },
    sendReturnButton: function(){
      var vue = this;
      this.showReturnDialog = false;
      this.$EventBus.$emit('sendReturn', vue.dialogInfo.rent_index);
    }
  }
};
</script>
