<template>


  <div class="content">
    <div class="md-layout">

      <div class="md-layout-item md-size-100">
        <md-card>

          <md-card-header data-background-color="orange" v-if="!this._props.englishSwitch_Tab">
            <h4 class="title">재고 현황</h4>
            <p class="category">현재 재고 현황 보여주기</p>
          </md-card-header>

          <md-card-header data-background-color="orange" v-if="this._props.englishSwitch_Tab">
            <h4 class="title">Stock</h4>
            <p class="category">Show the present condition of stock</p>
          </md-card-header>

          <md-card-content>

            <stock-table table-header-color="red" :userInfo_Table="userInfo_Tab" :englishSwitch_Table="englishSwitch_Tab"></stock-table>
          </md-card-content>

        </md-card>
      </div>

      <div class="md-layout-item md-size-50">
        <chart-card
        :chart-data="dailySalesChart.data"
        :chart-options="dailySalesChart.options"
        chart-type="Line"
        data-background-color="green">
        <template slot="content"  v-if="!this._props.englishSwitch_Tab">
          <h4 class="title">대여 현황</h4>
          <p class="category">
            <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> 전월 대비 증가
          </p>
        </template>
        <template slot="content"  v-if="this._props.englishSwitch_Tab">
          <h4 class="title">Rent Chart</h4>
          <p class="category">
            <span class="text-success"><i class="fa fa-long-arrow-up"></i> 55% </span> Increase the previous month.
          </p>
        </template>

        <template slot="footer">
          <div class="stats"  v-if="!this._props.englishSwitch_Tab">
            <md-icon>access_time</md-icon>
            4분 전 업데이트
          </div>
          <div class="stats"  v-if="this._props.englishSwitch_Tab">
            <md-icon>access_time</md-icon>
            Updated 4 minutes ago
          </div>
        </template>
      </chart-card>
    </div>

    <div class="md-layout-item md-size-50">
      <chart-card
      :chart-data="dailySalesChart2.data"
      :chart-options="dailySalesChart2.options"
      chart-type="Line"
      data-background-color="red">
      <template slot="content"  v-if="!this._props.englishSwitch_Tab">
        <h4 class="title">반납 연체 현황</h4>
        <p class="category">
          <span class="text-success"><i class="fa fa-long-arrow-up"></i> 10% </span> 전월 대비 감소
        </p>
      </template>
      <template slot="content"  v-if="this._props.englishSwitch_Tab">
        <h4 class="title">Overdue Chart</h4>
        <p class="category">
          <span class="text-success"><i class="fa fa-long-arrow-up"></i> 10% </span> Decrease the previous month.
        </p>
      </template>

      <template slot="footer">
        <div class="stats"  v-if="!this._props.englishSwitch_Tab">
          <md-icon>access_time</md-icon>
          Updated 4 minutes ago
        </div>
      </template>
    </chart-card>
    </div>
  </div>
</div>
</template>

<script>
import {
  StockTable,
  ChartCard
} from "@/components";

export default {
  props: {
    userInfo_Tab: Object,
    englishSwitch_Tab: Boolean
  },
  components: {
    StockTable,
    ChartCard
  },
  data() {
    return {
      dailySalesChart: {
        data: {
          labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
          series: [
            [12, 17, 7, 17, 23, 18, 38]
          ]
        },
        options: {
          lineSmooth: this.$Chartist.Interpolation.cardinal({
            tension: 0
          }),
          low: 0,
          high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
          }
        }
      },
      dailySalesChart2: {
        data: {
          labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
          series: [
            [15, 12, 7, 5, 8, 10, 9]
          ]
        },
        options: {
          lineSmooth: this.$Chartist.Interpolation.cardinal({
            tension: 0
          }),
          low: 0,
          high: 20, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
          chartPadding: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
          }
        }
      }
    };
  },
  created(){
    console.log("StockDashboardTab");
    console.log(this._props);
  }
};
</script>
