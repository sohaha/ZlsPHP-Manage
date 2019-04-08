<style>
</style>
<template>
  <div>
    <div class="view-title float-clear no">
      <span v-once>{{title}}</span>
      <div class="view-title-right"></div>
    </div>
    <el-tabs class="reply-tabs" v-model="activeName" @tab-click="handleClick">
      <el-tab-pane :key="k" v-for="(v,k) in tabs" :label="v" :name="k"></el-tab-pane>
    </el-tabs>
    <div class="panel">{{tabs[activeName]}}</div>
  </div>
</template>
<script>
var title = "标签页示例",
  that;
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        title: title,
        SpaTitle: title + " - %s",
        activeName: "key3",
        tabs: {
          key1: "标签页1",
          key2: "标签页2",
          key3: "标签页3"
        }
      };
    },
    created: function() {
      that = this;
    },
    mounted: function() {},
    computed: {},
    init: function(query, search) {},
    methods: {
      handleClick: function(tab, event) {
        console.log(tab, event);
      }
    }
  },
  [],
  "/index"
);
</script>
