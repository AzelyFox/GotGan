<template>


  <div class="content">
    <div class="md-layout">

      <div class="md-layout-item">
        <md-card>

          <md-card-header data-background-color="orange">
            <h4 class="title">재고 현황</h4>
            <p class="category">현재 재고 현황 보여주기</p>
          </md-card-header>

          <md-card-content>

            <stock-table table-header-color="red" :userInfo_Table="userInfo_Tab"></stock-table>
          </md-card-content>

        </md-card>
      </div>

    </div>
  </div>
</template>

<script>
import {
  StockTable
} from "@/components";

export default {
  props: {
    userInfo_Tab: Object
  },
  components: {
    StockTable
  },
  data() {
    return {
    };
  },
  created(){
    console.log(this._props);
  }
};
</script>
