<template>
  <li>

    <div :class="{bold: isFolder}" @click="toggle">
      <md-button class="md-raised" data-background-color="blue">허가</md-button>
    </div>

    <ul v-show="open" v-if="isFolder">

      <md-table>
        <md-table-row slot="md-table-row">
          <md-table-cell md-label="ID">ID</md-table-cell>
          <md-table-cell md-label="종류">Group</md-table-cell>
          <md-table-cell md-label="보관중">Store</md-table-cell>
          <md-table-cell md-label="대여중">Rent</md-table-cell>
          <md-table-cell md-label="수리중">Refair</md-table-cell>
          <md-table-cell md-label="총 수량">Amount</md-table-cell>
        </md-table-row>
      </md-table>

    </ul>

  </li>
</template>

<script>
export default {
  props: {
    model: Object
  },
  data: function () {
    return {
      open: false
    }
  },
  computed: {
    isFolder: function () {
      return this.model.children &&
        this.model.children.length
    }
  },
  methods: {
    toggle: function () {
      if (this.isFolder) {
        this.open = !this.open
      }
    },
    changeType: function () {
      if (!this.isFolder) {
        Vue.set(this.model, 'children', [])
        this.addChild()
        this.open = true
      }
    },
    addChild: function () {
      this.model.children.push({
        name: 'new stuff'
      })
    }
  }

};
</script>
