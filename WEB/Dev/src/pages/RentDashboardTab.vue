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
            <rent-request-table table-header-color="red"></rent-request-table>
          </md-card-content>
        </md-card>

        <md-card>
          <md-card-header data-background-color="red">
            <h4 class="title">반출 현황</h4>
            <p class="category">현재 반출되어 있는 재고 보여주기</p>
          </md-card-header>
          <md-card-content>
            <rent-status-table table-header-color="red"></rent-status-table>
          </md-card-content>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
import {
  RentRequestTable,
  RentStatusTable
} from "@/components";

export default {
  components: {
    RentRequestTable,
    RentStatusTable
  }
};
</script>
