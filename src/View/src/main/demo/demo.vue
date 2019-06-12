<style>
</style>
<!-- 路径是基于项目路径 -->
<link href="styles/demo.css" />
<template>
  <div>
    <div class="view-title float-clear">
      <!--占位-->
    </div>
    <fieldset>
      <legend>开发说明</legend>
      <aside class="center" aria-label="开发说明">
        <qriously :size="160" value="https://docs.73zls.com/zls-php/#/packages-manage/started"></qriously>开发手册
        <a
          href="https://docs.73zls.com/zls-php/#/packages-manage/started"
          target="_blank"
        >zls-manage</a>
        <br>UI 文档
        <a
          href="http://element-cn.eleme.io/#/zh-CN/component/layout"
          target="_blank"
        >element@2.6.1</a>
        <br>定制主题
        <a
          href="https://elementui.github.io/theme-chalk-preview/#/zh-CN"
          target="_blank"
        >#2c6eb1</a>
        <br>字体图标库
        <a href="https://www.iconfont.cn/collections/detail?cid=14834" target="_blank">iconfont</a>
      </aside>
    </fieldset>
    <div>
      <components_demo></components_demo>
    </div>
  </div>
</template>
<script>
var loadJs = ["//cdn.jsdelivr.net/npm/zls-manage/qriously/qriously.js"];
var title = "Demo";
Spa.define(
  {
    name: "Demo-View",
    mixins: [mixinLists],
    data: function() {
      return {
        title: title,
        SpaTitle: title + " - %s"
      };
    },
    beforeCreate: function() {
      that = this;
    },
    mounted: function() {},
    computed: {},
    init: function(query, search) {},
    methods: {}
  },
  ["/components/demo"],
  "/index"
);
</script>
