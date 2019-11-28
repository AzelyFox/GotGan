<template>
  <div class="content">
    <div class="md-layout">
      <div class="md-layout-item">
        <md-card class="md-card-plain">
          <md-card-header data-background-color="green">
            <h4 class="title" v-if="!this._props.englishSwitch_Tab">언어 변환</h4>
            <h4 class="title" v-if="this._props.englishSwitch_Tab">Language Change</h4>
            <md-switch v-model="englishSwitch" @change="switchLanguage">English</md-switch>
          </md-card-header>
        </md-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: {
      userInfo_Tab: Object,
      englishSwitch_Tab: Boolean
    },
    data() {
      return{
        englishSwitch: false
      }
    },
    created() {
      this.englishSwitch = this._props.englishSwitch_Tab;
          console.log(this._props.englishSwitch_Tab);
    },
    updated() {
    },
    methods: {
      switchLanguage: function(){
        this.$EventBus.$emit('changeLanguage');
      }
    }
};
</script>
