<template>
  <transition name="fade" mode="out-in">
    <router-view :userInfo_Tab="userInfo_Content"></router-view>
  </transition>
</template>
<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}

.fade-enter,
  .fade-leave-to
    /* .fade-leave-active in <2.1.8 */

 {
  opacity: 0;
}
</style>
<script>
export default {
  props: {
    userInfo_Content: Object
  },
  data(){
    return{
    };
  },
  created(){
    console.log("content");
    console.log(this._props.userInfo_Content);
  }
};
</script>
