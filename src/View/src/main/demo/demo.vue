<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span v-once>{{title}} </span>
    </div>
    <div class="panel">
      UI 文档 <a href="http://element-cn.eleme.io/#/zh-CN/component/layout" target="_blank">element@2.6.1</a> <br /><br /> 定制主题 <a href="https://elementui.github.io/theme-chalk-preview/#/zh-CN" target="_blank">#2c6eb1</a> <br /><br /> 字体图标库 <a href="https://www.iconfont.cn/collections/detail?cid=14834" target="_blank">iconfont</a>
    </div>
  </div>
</template>
<script>
  var title = 'Demo',
    that;
  Spa.define(
    {
      mixins: [mixinLists],
      data: function () {
        return {
          title: title,
          SpaTitle: title + ' - %s'
        };
      },
      created: function () {
        that = this;
      },
      mounted: function () {
      },
      computed: {},
      init: function (query, search) {
      },
      methods: {}
    },
    [],
    '/index'
  );
</script>
