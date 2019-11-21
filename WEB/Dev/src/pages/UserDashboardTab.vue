<template>


  <div class="content">
    <div class="md-layout">

      <div class="md-layout-item md-size-50">
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

      <div class="md-layout-item md-size-50">
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
                <md-table-head>시작 일자</md-table-head>
                <md-table-head>종료 일자</md-table-head>
              </md-table-row>

              <md-table-row v-for="item in rentList">
                <md-table-cell>{{ item.rent_product_name }}</md-table-cell>
                <md-table-cell>{{ item.rent_status }}</md-table-cell>
                <md-table-cell>{{ item.rent_time_start}}</md-table-cell>
                <md-table-cell>{{ item.rent_time_start}}</md-table-cell>
              </md-table-row>
            </md-table>
          </md-card-content>
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
              <md-select v-model="add_RentProduct" name="add_RentProduct" id="add_RentProduct" md-dense required @md-selected="checkSelectedProduct">
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
            <md-button @click="sendRentData">대여 신청</md-button>
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

        <!--
        <md-field>
          <label v-if="modifyUserInfo">그룹</label>
          <label v-if="!modifyUserInfo">{{ _props.userInfo_Tab.user_group_name }}</label>
          <md-input v-model="userInfo.group" v-if="modifyUserInfo"></md-input>
        </md-field>
₩      -->
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
        <!--
        user_pw (string) [선택 인자]
        수정할 유저의 비밀번호를 의미한다.

        user_name (string) [선택 인자]
        수정할 유저의 이름을 의미한다.

        user_group (int) [선택 인자]
        수정할 유저가 속할 그룹을 의미한다.

        user_sid (string) [선택 인자]
        수정할 유저의 학번을 의미한다.

        user_email (string) [선택 인자]
        수정할 유저의 이메일을 의미한다.

        user_phone (string) [선택 인자]
        수정할 유저의 휴대기기 번호를 의미한다.
      -->
      </md-dialog-content>
      <md-dialog-actions>
        <md-button class="md-primary" @click="modifyButton" v-if="!modifyUserInfo">수정하기</md-button>
        <md-button class="md-primary" @click="showDialog = false" v-if="!modifyUserInfo">닫기</md-button>

        <md-button class="md-primary" @click="modifySendButton" v-if="modifyUserInfo">수정전송</md-button>
        <md-button class="md-primary" @click="cancleButton" v-if="modifyUserInfo">취소하기</md-button>
      </md-dialog-actions>
    </md-dialog>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    userInfo_Tab: Object
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
      }
    };
  },
  created(){
    console.log("UserDashboardTab");
    console.log(this._props);

    this.userInfo = {
      name: this._props.userInfo_Tab.user_name,
      //group: this._props.userInfo_Tab.user_group_name,
      sID: this._props.userInfo_Tab.user_sid,
      email: this._props.userInfo_Tab.user_email,
      phone: this._props.userInfo_Tab.user_phone
    };
    //this.modifyUserInfo = false;

    console.log(this.modifyUserInfo);
    console.log(this.userInfo);

    this.exportRentData(this._props.userInfo_Tab.session);
    this.exportProductData(this._props.userInfo_Tab.session);
  },
  methods: {
    // 대여 현황 받아오기
    exportRentData: function(session){
      var vue = this;
      var rentParams = new URLSearchParams();
      rentParams.append('session', session);

      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', rentParams)
      .then(function(response) {
        console.log(response.data);
        var userIndex = vue._props.userInfo_Tab.user_index;
        for(var x = 0; x < response.data.rents.length; x++){
          if(userIndex == response.data.rents[x].rent_user_index){
            vue.rentList.push(response.data.rents[x]);
          }
        }
        console.log(vue.rentList);
      })
      .catch(function(error) {
        console.log(error);
      });
    },
    // 재고와 그룹 데이터 받아오기
    exportProductData: function(session){
      var vue = this;
      var productParams = new URLSearchParams();
      productParams.append('session', session);

      axios.post('https://api.devx.kr/GotGan/v1/product_list.php', productParams)
      .then(function(response) {
        console.log(response.data);
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
    // 대여 신청 전송
    sendRentData: function(){
      var vue = this;
      var rentParams = new URLSearchParams();
      rentParams.append('session', vue._props.userInfo_Tab.session);
      rentParams.append('rent_product', vue.add_RentProduct);
      rentParams.append('rent_user', vue._props.userInfo_Tab.user_index);
      rentParams.append('rent_time_start', vue.add_RentStartDay);

      axios.post('https://api.devx.kr/GotGan/v1/rent_add.php', rentParams)
      .then(function(response) {
        console.log(response.data);
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
      userModifyParams.append('session', vue._props.userInfo_Tab.session);
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
    }
  },
  updated() {
    // 시작 날짜, 종료 날짜 계산
    this.calculateStartDay();

    this.showDialog ? 0 : this.resetModifyInfo();
  }
};
</script>
