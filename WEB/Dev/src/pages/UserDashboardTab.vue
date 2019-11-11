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
            <md-button>정보 관리</md-button>
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
              <label for="add_RentProduct">대여할 물품</label>
              <md-select v-model="add_RentProduct" name="add_RentProduct" id="add_RentProduct" md-dense required>
                <md-option value="3D 프린터">3D 프린터</md-option>
              </md-select>
            </md-field>

            <div class="md-layout-item md-size-5"></div>

            <div class="md-layout-item md-size-25">
              <md-datepicker v-model="input_RentStartDay" md-closed="calculateEndDay" md-immediately>
                <label>시작일</label>
              </md-datepicker>
            </div>

            <div class="md-layout-item md-size-5"></div>

            <md-field class="md-layout-item md-size-25">
              <label>반납일</label>
              <md-input v-model="add_RentEndDay" disabled style="margin-top: 14px"></md-input>
            </md-field>
            <!--
            session (string) [필수 인자]
            세션 인증이 필요하다.

            product_barcode (int) [선택 인자]
            대여할 물품의 바코드 번호를 의미한다.
            이 값이 있는 경우 rent_product 는 선택 인자가 된다.

            rent_product (int) [준 필수 인자]
            대여할 물품의 인덱스 번호를 의미한다.
            이 값이 있는 경우 product_barcode 보다 우선시된다.
            이 값과 product_barcode 값 둘 다 없는 경우 에러가 반환된다.

            rent_user (int) [선택 인자]
            대여하는 유저의 인덱스를 의미한다.
            관리자 이상 등급만 타인을 rent_user로 설정이 가능하다.
            값이 없을 경우 세션에서 로그인 정보를 기반으로 사용자를 가져온다.

            rent_time_start (string) [필수 인자]
            대여할 시작 일자를 의미한다.
            yyyy-MM-dd HH:mm:ss 형식으로 전달이 필요하다.
          -->
          </md-card-content>

          <md-card-actions>
            <md-button>대여 신청</md-button>
          </md-card-actions>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    userInfo_Tab: Object
  },
  data() {
    return {
      rentList: [],
      add_RentProduct: "",
      input_RentStartDay: null,
      add_RentStartDay: "",
      add_RentEndDay: ""
    };
  },
  created(){
    console.log("UserDashboardTab");
    console.log(this._props);

    this.exportRentData(this._props.userInfo_Tab.session);
  },
  methods: {
    exportRentData: function(session){
      var vue = this;
      var rentParams = new URLSearchParams();
      rentParams.append('session', session);

      axios.post('https://api.devx.kr/GotGan/v1/rent_list.php', rentParams)
      .then(function(response) {
        var userIndex = vue._props.userInfo_Tab.user_index;
        for(var x = 0; x < response.data.rents.length; x++){
          if(userIndex == response.data.rents[x].rent_user_index){
            vue.rentList.push(response.data.rents[x]);
          }
        }

      })
      .catch(function(error) {
        console.log(error);
      });
    },
    calculateStartDay: function(){
      var returnText = "";
      try {
        var month, day = "";
        var num = this.input_RentStartDay.getMonth() + 1;
        num < 10 ? month = "0" + num : month = num;
        this.input_RentStartDay.getDate() < 10 ? day =  "0" + this.input_RentStartDay.getDate() : day =this.input_RentStartDay.getDate();

        returnText = this.input_RentStartDay.getFullYear() + "-" + month + "-" + day + " 00:00:00";
        
        this.add_RentEndDay = returnText;
      } catch (e) {
      }
    },
    calculateEndDay: function(){

      console.log("test");
      return "hello";
    }
  },
  updated() {
    console.log(this.input_RentStartDay);
    this.calculateStartDay();
  }
};
</script>
