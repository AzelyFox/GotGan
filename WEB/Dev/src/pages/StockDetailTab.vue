<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card>

          <md-card-header data-background-color="red">
            <h4 class="title">재고 상세</h4>
            <p class="category">상세한 재고 보여주기</p>
          </md-card-header>

          <md-card-content>
            <stock-detail-table></stock-detail-table>
          </md-card-content>

        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
import {
  StockDetailTable
} from "@/components/";

export default {
  components: {
    StockDetailTable
  }
};
</script>
