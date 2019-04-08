<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span v-once>{{title}} </span>
    </div>
    <fieldset>
      <legend>开发说明</legend>
      <aside class='center'>
        <qriously :size="120" value="https://docs.73zls.com/zls-php/#/packages-manage/started"></qriously>
        开发手册 <a href="https://docs.73zls.com/zls-php/#/packages-manage/started" target="_blank">zls-manage</a><br />
        UI 文档 <a href="http://element-cn.eleme.io/#/zh-CN/component/layout" target="_blank">element@2.6.1</a> <br /> 定制主题 <a href="https://elementui.github.io/theme-chalk-preview/#/zh-CN" target="_blank">#2c6eb1</a> <br />
        字体图标库 <a href="https://www.iconfont.cn/collections/detail?cid=14834" target="_blank">iconfont</a>
      </aside>
    </fieldset>
  </div>
</template>
<script>
  var loadJs = ['//cdn.jsdelivr.net/npm/zls-manage/qriously/qriously.js'];
  var title = 'Demo',
    that;
  Spa.define(
    {
      name: 'Demo-View',
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
