<template>
  <md-toolbar md-elevation="0" class="md-transparent">
    <div class="md-toolbar-row">
      <div class="md-toolbar-section-start">
        <h3 class="md-title" v-if="!_props.englishSwitch_Top">{{ $route.name }}</h3>
        <h3 class="md-title" v-if="_props.englishSwitch_Top">{{ name }}</h3>
      </div>
      <!-- 반응형 같음 -->
      <div class="md-toolbar-section-end">
        <md-button
        class="md-just-icon md-simple md-toolbar-toggle"
        :class="{ toggled: $sidebar.showSidebar }"
        @click="toggleSidebar"
        >
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </md-button>

      <div class="md-collapse">

        <md-list>
          <md-list-item>
            <h3 class="md-title">{{ userName_Top }}</h3>
          </md-list-item>



          <md-list-item @click="logoutButton">
            <i class="material-icons">logout</i>
            <p class="hidden-lg hidden-md">Logout</p>
          </md-list-item>
        </md-list>
      </div>
    </div>
  </div>
</md-toolbar>
</template>

<script>
import axios from 'axios';
import router from '../../main.js'

export default {
  props: {
    userName_Top: String,
    englishSwitch_Top: Boolean
  },
  data() {
    return {
      session: "",
      name: ""
    };
  },
  created() {
    var vue = this;
    this.session = this.getCookie("session");

    this.$EventBus.$on('changeTitle', () => {
      vue.changeTitle();
    });
  },
  methods: {
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
    },
    logoutButton() {
      var logoutParams = new URLSearchParams();
      var vue = this;
      logoutParams.append('session', this.session);

      axios.post('https://api.devx.kr/GotGan/v1/login.php', logoutParams)
      .then(function(response) {
        console.log(response.data);
        vue.deleteCookie("session");
        router.push("/");
      })
      .catch(function(error) {
        console.log(error);
      });

    },
    getCookie: function(_name) {
      var value = document.cookie.match('(^|;) ?' + _name + '=([^;]*)(;|$)');
      return value? value[2] : null;
    },
    deleteCookie: function(_name) {
      var date = new Date();
      document.cookie = _name + "= " + "; expires=" + date.toUTCString() + "; path=/";
    },
    changeTitle: function(){
      switch (this.$route.name) {
        case "재고 대시보드":
          this.name = "Stock Dashboard";
        break;
        case "재고 상세":
          this.name = "Stock Detail";
        break;
        case "반출입 대시보드":
          this.name = "Rent Dashboard";
        break;
        case "유저 관리":
          this.name = "Uset Management";
        break;
        case "설정":
          this.name = "Setting";
        break;
        default:

      }
    }
  }
};
</script>

<style lang="css">

.name {
}

</style>
