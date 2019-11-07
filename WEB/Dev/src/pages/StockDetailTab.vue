<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <!-- 재고 상세 테이블 카드-->
        <md-card>
          <md-card-header data-background-color="red">
            <h4 class="title">재고 상세</h4>
            <p class="category">상세한 재고 보여주기</p>
          </md-card-header>

          <md-card-content>
            <stock-detail-table :userInfo_Table="userInfo_Tab"></stock-detail-table>
          </md-card-content>
        </md-card>

        <!-- 재고 상세 테이블 카드-->
        <md-card>
          <md-card-header data-background-color="red">
            <h4 class="title">재고 추가</h4>
            <p class="category">재고 추가하기</p>
          </md-card-header>

          <md-card-content>
            <div class="md-layout md-gutter">
              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_name">항목 이름</label>
                  <md-input name="product_name" id="product_name" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_group">그룹</label>
                  <md-select v-model="product_group" name="product_group" id="product_group" md-dense :disabled="sending">
                    <md-option value="group_add" @click="test">그룹 추가</md-option>
                    <md-option v-for="item in product_groups" v-bind:value="item.group_name">
                      {{ item.group_name }}
                    </md-option>
                  </md-select>
                </md-field>
              </div>
            </div>

            <div class="md-layout md-gutter">

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_status">상태</label>
                  <md-input name="product_status" id="product_status" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_owner">소속</label>
                  <md-input name="product_owner" id="product_owner" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_barcode">바코드</label>
                  <md-input name="product_barcode" id="product_barcode" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>
            </div>
            <div class="md-layout md-gutter" v-if="showGroupAdd">

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_group_name">그룹 이름</label>
                  <md-input name="product_group_name" id="product_group_name" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_group_rentable">대여 가능 일수</label>
                  <md-input name="product_group_rentable" id="product_group_rentable" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>

              <div class="md-layout-item md-small-size-100">
                <md-field >
                  <label for="product_group_priority">중요도</label>
                  <md-input name="product_group_priority" id="product_group_priority" autocomplete="family-name" :disabled="sending" />
                </md-field>
              </div>
            </div>
            <!--

            products는 아래 값들로 이루어진 JSONObject 의 Array 이다.

            product_group (int) [필수 인자]
            물품이 속하는 product_group_index 를 의미한다.

            product_name (string) [선택 인자]
            물품의 개별 이름을 의미한다.
            값이 없을 경우 물품 그룹명을 그대로 가져온다.

            product_status (int) [선택 인자]
            물품의 상태 코드를 의미한다.
            0 : 일반
            1 : 사용불가
            2 : 고장
            3 : 수리중
            값이 없을 경우 기본값인 0으로 입력된다.

            product_owner (int) [선택 인자]
            물품을 소유하는 유저그룹을 의미한다.
            값이 없을 경우 0으로 입력된다.

            product_barcode (int) [선택 인자]
            물품의 바코드 번호를 의미한다.

            UNDER group

            product_group_name (string) [필수 인자]
            추가할 물품 그룹의 그룹명을 의미한다.

            product_group_rentable (int) [필수 인자]
            추가할 물품 그룹이 며칠동안 대여가 가능한지를 의미한다.

            product_group_priority (int) [선택 인자]
            추가할 물품 그룹의 중요도를 의미한다.
            값이 없을 경우 0이 기본으로 입력된다.
          -->
            <md-card-actions>
              <md-button type="submit" class="md-primary" :disabled="sending" @click="test">재고 추가</md-button>
            </md-card-actions>


            <!--md-snackbar :md-active.sync="userSaved">The user  was saved with success!</md-snackbar-->
          </md-card-content>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

var params = new URLSearchParams();
import {
  StockDetailTable
} from "@/components/";

export default {
  props: {
    userInfo_Tab: Object
  },
  components: {
    StockDetailTable
  },
  data() {
    return {
      sending: false,
      product_groups: [],
      product_group: "",
      showGroupAdd: false
    };
  },
  created(){
    console.log("StockDetailTab");
    console.log(this._props);
    params.append('session', this._props.userInfo_Tab.session);
    this.exportData(params);

  },
  methods: {
    exportData: function(){
      var vue = this;
      axios.post('https://api.devx.kr/GotGan/v1/product_overview.php', params)
      .then(function(response) {
        console.log(response.data.groups);
          console.log(response.data.groups.length);
          for(var x = 0; x < response.data.groups.length; x++){
            vue.product_groups.push(response.data.groups[x]);
          }
          console.log(vue);

      })
      .catch(function(error) {
        console.log(error);
      });
    },
    test: function(){
      console.log(this.product_group);
      console.log("Hi");
    }
  },
  updated(){
    if(this.product_group == "group_add"){
      this.showGroupAdd = true;
      console.log(this.product_group);
    }else{
      this.showGroupAdd = false;
    }
  }
};
</script>
