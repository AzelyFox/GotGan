<template>
  <router-view :_userInfo="userInfo" @child="parents"></router-view>
</template>

<script>
export default {
  data(){
    return{
      userInfo: {}
    }
  },
  methods:{
    parents(params){
      this.userInfo = params;
    }
  }
};
</script>
