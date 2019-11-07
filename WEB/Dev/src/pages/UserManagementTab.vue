<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card>
          <md-card-header data-background-color="green">
            <h4 class="title">유저 관리</h4>
            <p class="category">유저 관리 관련 카드</p>
          </md-card-header>
          <md-card-content>
            <p>관리하자</p>
          </md-card-content>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    dataBackgroundColor: {
      type: String,
      default: ""
    },
    userInfo_Tab: Object
  }
};
</script>
