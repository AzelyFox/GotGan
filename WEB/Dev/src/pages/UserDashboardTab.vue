<template>
  <div class="content">
    <div class="md-layout">

      <div class="md-layout-item md-size-40">
        <md-card>
          <md-card-header data-background-color="orange">
            <h4 class="title">개인 정보</h4>
            <p class="category">개인 정보 보여주기</p>
          </md-card-header>

          <md-card-content>
            <div>이름 : {{ _props.userInfo_Tab.user_name }}</div>

            <div>전화번호 : {{ _props.userInfo_Tab.user_phone }}</div>

            <div>이메일 : {{ _props.userInfo_Tab.user_email }}</div>

            <div>학번 : {{ _props.userInfo_Tab.user_sid }}</div>
          </md-card-content>

          <md-card-actions>
            <md-button @click="showDialog = true">정보 관리</md-button>
          </md-card-actions>
        </md-card>
      </div>

      <div class="md-layout-item md-size-60">
        <md-card>
          <md-card-header data-background-color="orange">
            <h4 class="title">대여 현황</h4>
            <p class="category">대여 현황 보여주기</p>
          </md-card-header>

          <md-card-content>
            <md-table>
              <md-table-row>
                <md-table-head>이름</md-table-head>
                <md-table-head>대여 상태</md-table-head>
                <md-table-head>대여 일자</md-table-head>
                <md-table-head>반납 예정일</md-table-head>
                <md-table-head>반납 완료일</md-table-head>
              </md-table-row>

              <md-table-row v-for="item in rentList" v-if="item.rent_status == 0 && showPostRent">
                <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
                <md-table-cell>{{ item.status }}</md-table-cell>
                <md-table-cell>{{ item.rent_time_start}}</md-table-cell>
                <md-table-cell>{{ item.rent_time_end}}</md-table-cell>
                <md-table-cell>{{ item.rent_time_return}}</md-table-cell>
              </md-table-row>

              <md-table-row v-for="item in rentList" v-if="item.rent_status != 0">
                <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
                <md-table-cell>{{ item.status }}</md-table-cell>
                <md-table-cell>{{ item.rent_time_start}}</md-table-cell>
                <md-table-cell>{{ item.rent_time_end}}</md-table-cell>
                <md-table-cell>{{ item.rent_time_return}}</md-table-cell>
              </md-table-row>
            </md-table>
          </md-card-content>

          <md-card-actions>
            <md-button @click="postRentLisrButton" v-if="!showPostRent">이전 대여 항목 조회</md-button>
            <md-button @click="postRentLisrButton" v-if="showPostRent">이전 대여 항목 닫기</md-button>
          </md-card-actions>
        </md-card>
      </div>

      <div class="md-layout-item md-size-100">
        <md-card>
          <md-card-header data-background-color="orange">
            <h4 class="title">대여 신청</h4>
            <p class="category">대여 신청 가능</p>
          </md-card-header>

          <md-card-content class="md-layout">
            <md-field class="md-layout-item md-size-25">
              <label for="add_RentGroup">대여할 물품 그룹</label>
              <md-select v-model="add_RentGroup" name="add_RentGroup" id="add_RentGroup" md-dense required @md-selected="clearSelectedProduct">
                <md-option v-for="item in groupList" v-bind:value="item.group_index" >
                  {{ item.group_name }}
                </md-option>
              </md-select>
            </md-field>

            <div class="md-layout-item md-size-5"></div>

            <div class="md-layout-item md-size-25">
              <md-datepicker v-model="input_RentStartDay" md-immediately>
                <label>시작일</label>
              </md-datepicker>
            </div>

            <div class="md-layout-item md-size-45"></div>

            <md-field class="md-layout-item md-size-25">
              <label for="add_RentProduct">상세 물품 선택</label>
              <md-select v-model="add_RentProduct" name="add_RentProduct" id="add_RentProduct" md-dense required @md-selected="checkSelectedProduct" v-if="this.add_RentGroup.length != 0">
                <md-option v-for="item in productList" v-bind:value="item.product_index" v-if="item.product_group_index == add_RentGroup">
                  {{ item.product_name }}
                </md-option>
              </md-select>
            </md-field>

            <div class="md-layout-item md-size-5"></div>

            <md-field class="md-layout-item md-size-25">
              <label>반납일</label>
              <md-input v-model="add_RentEndDay" disabled style="margin-top: 14px"></md-input>
            </md-field>
          </md-card-content>

          <md-card-actions>
            <md-button @click="sendRentButton">대여 신청</md-button>
          </md-card-actions>
        </md-card>
      </div>
    </div>

    <md-dialog :md-active.sync="showDialog">
      <md-dialog-title>개인 정보 관리</md-dialog-title>

      <md-dialog-content>
        <md-field>
          <label v-if="modifyUserInfo">이름</label>
          <label v-if="!modifyUserInfo">{{ _props.userInfo_Tab.user_name }}</label>
          <md-input v-model="userInfo.name" v-if="modifyUserInfo"></md-input>
        </md-field>

        <md-field>
          <label v-if="modifyUserInfo">학번</label>
          <label v-if="!modifyUserInfo">{{ _props.userInfo_Tab.user_sid }}</label>
          <md-input v-model="userInfo.sID" v-if="modifyUserInfo"></md-input>
        </md-field>

        <md-field>
          <label v-if="modifyUserInfo">이메일</label>
          <label v-if="!modifyUserInfo">{{ _props.userInfo_Tab.user_email }}</label>
          <md-input v-model="userInfo.email" v-if="modifyUserInfo"></md-input>
        </md-field>

        <md-field>
          <label v-if="modifyUserInfo">전화번호</label>
          <label v-if="!modifyUserInfo">{{ _props.userInfo_Tab.user_phone }}</label>
          <md-input v-model="userInfo.phone" v-if="modifyUserInfo"></md-input>
        </md-field>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="modifyButton" v-if="!modifyUserInfo">수정하기</md-button>
        <md-button class="md-primary" @click="showDialog = false" v-if="!modifyUserInfo">닫기</md-button>

        <md-button class="md-primary" @click="modifySendButton" v-if="modifyUserInfo">수정전송</md-button>
        <md-button class="md-primary" @click="cancleButton" v-if="modifyUserInfo">취소하기</md-button>
      </md-dialog-actions>
    </md-dialog>

    <md-dialog :md-active.sync="showRentDialog">
      <md-dialog-title>대여 신청</md-dialog-title>

      <md-dialog-content>
        <p>{{rentProductName}}</p>
        <p>{{add_RentStartDay}}</p>
      </md-dialog-content>

      <md-dialog-actions>
        <md-button class="md-primary" @click="sendRentData">대여 신청</md-button>
        <md-button class="md-primary" @click="showRentDialog = false">취소</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import axios from 'axios';

var params = new URLSearchParams();

var propsEmpty = false;


export default {
  props: {
    userInfo_Tab: Object,
    englishSwitch_Tab: Boolean
  },
  components: {
  },
  data() {
    return {
      rentList: [],
      add_RentGroup: "",
      add_RentProduct: "",
      rentableDay: 0,
      input_RentStartDay: null,
      add_RentStartDay: "",
      add_RentEndDay: "",
      groupList: [],
      productList: [],
      showDialog: false,
      modifyUserInfo: false,
      userInfo: {
        name: "",
        //group: "",
        sID: "",
        email: "",
        phone: ""
      },
      session: "",
      showPostRent: false,
      showRentDialog: false,
      rentProductName: ""
    };
  },
  created(){
    console.log("UserDashboardTab");
    console.log(this._props.userInfo_Tab);


    params.append('session', this.getCookie("session"));

    this.userInfo = {
      name: this._props.userInfo_Tab.user_name,
      //group: this._props.userInfo_Tab.user_group_name,
      sID: this._props.userInfo_Tab.user_sid,
      email: this._props.userInfo_Tab.user_email,
      phone: this._props.userInfo_Tab.user_phone
    };

    this.exportRentData(params);
    this.exportProductData(params);
  },
  methods: {
    // 대여 현황 받아오기
    exportRentData: function(param){
      var vue = this;

      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', param)
      .then(function(response) {
        vue.rentList = [];
        var userIndex = vue._props.userInfo_Tab.user_index;
        if(userIndex != null){
          for(var x = 0; x < response.data.rents.length; x++){
            if(userIndex == response.data.rents[x].rent_user_index){
              var obj = response.data.rents[x];
              obj.rent_time_start = obj.rent_time_start.slice(0, 10);
              obj.rent_time_end != null ? obj.rent_time_end = obj.rent_time_end.slice(0, 10) : 0;
              obj.rent_time_return != null ? obj.rent_time_return = obj.rent_time_return.slice(0, 10) : 0;

              switch (obj.rent_status) {
                case 0:
                obj.status = "완료됨";
                break;
                case 1:
                obj.status = "대여 신청됨";
                break;
                case 2:
                obj.status = "대여중";
                break;
              }
              vue.rentList.push(obj);
            }
          }
        }else{
          vue.exportRentData(params);
        }
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    // 재고와 그룹 데이터 받아오기
    exportProductData: function(param){
      var vue = this;

      axios.post('https://api.devx.kr/GotGan/v1/product_list.php', param)
      .then(function(response) {
        //console.log(response.data);
        for(var x = 0; x < response.data.groups.length;x++){
          vue.groupList.push(response.data.groups[x]);
        }
        for(var x = 0; x < response.data.products.length;x++){
          vue.productList.push(response.data.products[x]);
        }
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    // 대여 신청 버튼
    sendRentButton: function(){
      if(this.add_RentProduct == ""){
        alert("대여할 물품을 선택하시오.");
      }else if(this.add_RentStartDay == ""){
        alert("시작일을 선택하시오.");
      }else{
        for(var i in this.productList){
          if(this.productList[i].product_index == this.add_RentProduct)
          this.rentProductName = this.productList[i].product_name;
        }
        this.showRentDialog = true;
      }
    },
    // 대여 신청 전송
    sendRentData: function(){
      var rentParams = new URLSearchParams();
      var vue = this;
      rentParams.append('session', this.getCookie("session"));
      rentParams.append('rent_product', this.add_RentProduct);
      rentParams.append('rent_user', this._props.userInfo_Tab.user_index);
      rentParams.append('rent_time_start', this.add_RentStartDay);

      axios.post('https://api.devx.kr/GotGan/v1/rent_add.php', rentParams)
      .then(function(response) {
        //console.log(response.data);
        vue.showRentDialog = false;
        vue.exportRentData(params);
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    // 날짜 관련 계산
    calculateStartDay: function(){
      try {
        var month, day = "";
        var num = this.input_RentStartDay.getMonth() + 1;
        num < 10 ? month = "0" + num : month = num;
        this.input_RentStartDay.getDate() < 10 ? day =  "0" + this.input_RentStartDay.getDate() : day =this.input_RentStartDay.getDate();

        this.add_RentStartDay = this.input_RentStartDay.getFullYear() + "-" + month + "-" + day + " 00:00:00";
        var endDate = new Date(this.input_RentStartDay);
        endDate.setDate(parseInt(day) + this.rentableDay);


        num = endDate.getMonth() + 1;
        num < 10 ? month = "0" + num : month = num;
        endDate.getDate() < 10 ? day =  "0" + endDate.getDate() : day =endDate.getDate();


        this.add_RentEndDay = endDate.getFullYear() + "-" + month + "-" + day ;
      } catch (e) {
      }
    },
    //물품 그룹 선택시 상세 물품 선택 초기화
    clearSelectedProduct: function(){
      this.add_RentProduct = "";
    },
    // 상세 물품 선택시 대여 가능일수 확인
    checkSelectedProduct: function(){
      if(this.input_RentStartDay != null){
        this.input_RentStartDay = null;
        this.add_RentEndDay = "";
      }

      for(var i in this.groupList){
        this.groupList[i].group_index == this.add_RentGroup ? this.rentableDay = this.groupList[i].group_rentable : 0;
      }
    },
    modifyButton: function(){
      this.modifyUserInfo = true;
    },
    cancleButton: function(){
      this.modifyUserInfo = false;
    },
    modifySendButton: function(){
      var vue = this;
      var userModifyParams = new URLSearchParams();
      userModifyParams.append('session', vue.getCookie("session"));
      userModifyParams.append('user_index', vue._props.userInfo_Tab.user_index);
      userModifyParams.append('user_name', vue.userInfo.name);
      //userModifyParams.append('user_group', vue.userInfo.group);
      userModifyParams.append('user_sid', vue.userInfo.sID);
      userModifyParams.append('user_email', vue.userInfo.email);
      userModifyParams.append('user_phone', vue.userInfo.phone);

      axios.post('https://api.devx.kr/GotGan/v1/user_modify.php', userModifyParams)
      .then(function(response) {
        console.log(response.data);
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    resetModifyInfo: function(){
      this.modifyUserInfo = false;

      this.userInfo = {
        name: this._props.userInfo_Tab.user_name,
        //group: this._props.userInfo_Tab.user_group_name,
        sID: this._props.userInfo_Tab.user_sid,
        email: this._props.userInfo_Tab.user_email,
        phone: this._props.userInfo_Tab.user_phone
      };
    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    },
    postRentLisrButton: function(){
      this.showPostRent = !this.showPostRent;
    }
  },
  updated() {
    // 시작 날짜, 종료 날짜 계산
    this.calculateStartDay();

    this.showDialog ? 0 : this.resetModifyInfo();
  }
};
</script>
