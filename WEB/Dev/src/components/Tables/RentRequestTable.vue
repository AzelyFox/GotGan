<template>
  <div>
    <md-table v-model="users" :table-header-color="tableHeaderColor">
      <md-table-row slot="md-table-row" slot-scope="{ item }">
        <md-table-cell md-label="이름">{{ item.name }}</md-table-cell>
        <md-table-cell md-label="항목">{{ item.item }}</md-table-cell>
        <md-table-cell md-label="기간">{{ item.period }}</md-table-cell>
        <md-table-cell md-label="처리 버튼">
          <md-button class="md-raised" data-background-color="blue">허가</md-button>
          <md-button class="md-raised" data-background-color="red">거부</md-button>
        </md-table-cell>
      </md-table-row>
    </md-table>
  </div>
</template>

<script>
export default {
  name: "simple-table",
  props: {
    tableHeaderColor: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      selected: [],
      users: [
        {
          name: "김아무개",
          item: "3D 프린터",
          period: "19.10.16 ~ 19.10.23"
        }
      ]
    };
  }
};
</script>
