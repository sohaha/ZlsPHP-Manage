<style>
  .el-menu {
    border: 0;
  }

  .nav-left {
    background-color: #f5f7f9;
    overflow: hidden;
    -webkit-transition: width 0.35s cubic-bezier(0.37, 1.46, 0.61, 1.41);
    transition: width 0.35s cubic-bezier(0.37, 1.46, 0.61, 1.41);
    margin-right: 5px;
  }

  .nav-left .menu_title {
    font-size: 16px;
    margin-left: 10px;
  }

  .nav-left .el-menu .el-menu--inline .el-menu-item {

  }

  .nav-left .el-menu {
    background: #f6f8f9;
  }

  .nav-left .el-menu-item, .nav-left .el-submenu__title {
    margin-bottom: 5px;
  }

  .el-menu--collapse .el-submenu__title i,
  .el-menu-item > .el-tooltip > i {
    font-size: 35px;
    width: 34px;
  }

  .nav-left .el-menu-item:focus {
    background: none;
  }

  .el-menu--collapse .el-submenu__title,
  .el-menu-item > .el-tooltip {
    padding: 0 15px !important;
  }

  .nav_scrollbar {
    padding: 30px 0 10px 10px;
    max-height: calc(100vh - 60px);
    box-sizing: border-box;
    margin-bottom: -10px !important;
  }

</style>
<template>
  <el-scrollbar wrap-class="nav_scrollbar" wrap-style="" view-style="" view-class="view-box" :native="false">
    <el-menu :collapse="isCollapse" :default-active="active" @open="handleOpen" @close="handleClose" @select="handleSelect" text-color="#222" active-text-color="#2c6eb1" unique-opened>
      <template v-for="(v,k) in data">
        <el-submenu v-if="v.child" :index="v.index">
          <template slot="title">
            <i v-if="typeof v.icon!=='undefined'" :class="v.icon||'icon-flag'"></i> <span class="menu_title">{{ v.title }}</span>
          </template>
          <el-menu-item v-for="(vv,kk) in v.child" :key="kk" :index="vv.index"><i v-if="typeof vv.icon!=='undefined'" :class="vv.icon||'icon-flag'"></i>{{ vv.title }}</el-menu-item>
        </el-submenu>
        <el-menu-item v-else="v.child" :index="v.index">
          <i v-if="v.icon" :class="v.icon"></i> <span slot="title" class="menu_title">{{ v.title }}</span>
        </el-menu-item>
      </template>
    </el-menu>
  </el-scrollbar>
</template>
<!--suppress VueDuplicateTag, HtmlUnknownTarget -->
<script src='./script/nav.js'></script>
<!--suppress VueDuplicateTag -->
<script>
  var that, navLoadDefault;
  Spa.define({
    beforeDestroy: function () {
      navLoadDefault = false;
    },
    data: function () {
      var nav = [].concat(navs['global'], navs['customize'][this.$store.getters.groupID] || []);
      // Spa.title = userTypes;
      // this.$SpaSetTitle();
      if (Spa.debug && !navLoadDefault) {
        navLoadDefault = true;
        nav.push({
          title: '页面示例',
          index: '',
          icon: 'icon-folder',
          child: [
            { title: '默认示例', icon: '', index: 'demo/demo' },
            { title: '列表示例', icon: 'icon-list', index: 'demo/demo-lists' },
            { title: '列表编辑', icon: 'icon-edit', index: 'demo/demo-lists-edit' },
            { title: '标签示例', icon: 'icon-pricetags', index: 'demo/demo-tabs' },
            { title: '图标示例', icon: 'icon-award', index: 'demo/demo-icon' },
          ],
        });
      }
      return {
        data: nav,
      };
    },
    created: function () {
      that = this;
    },
    props: {
      isCollapse: {
        type: Boolean,
        default: false,
      },
    },
    computed: {
      active: function () {
        return this.router.page.slice(5);
      },
    },
    methods: {
      handleNav: function () {
        this.isCollapse = !this.isCollapse;
      },
      handleOpen: function (key, keyPath) {
        console.log(key, keyPath);
      },
      handleClose: function (key, keyPath) {
        console.log(key, keyPath);
      },
      handleSelect: function (key, keyPath) {
        console.log(key, keyPath);
        this.$go(key);
      },
    },
  });
</script>
