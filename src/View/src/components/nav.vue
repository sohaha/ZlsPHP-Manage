<style>
  .el-menu {
    border: 0;
  }

  .nav-left {
    /*background-color: #f5f7f9;*/
    background-color: #324157;
    overflow: hidden;
    -webkit-transition: width 0.35s cubic-bezier(0.55, 1.03, 0.54, 1.33);
    transition: width 0.35s cubic-bezier(0.55, 1.03, 0.54, 1.33);
    /*margin-right: 5px;*/
  }

  .nav-left .menu_title {
    font-size: 16px;
    margin-left: 10px;
  }

  .nav-left .el-scrollbar__wrap {
    overflow-x: hidden;
  }

  .nav-left .el-menu {
    /*background: #f6f8f9;*/
    background: #324157;
  }

  .nav-left .el-menu-item,
  .nav-left .el-submenu__title {
    margin-bottom: 5px;
  }

  .el-menu--collapse .el-submenu__title i,
  .el-menu-item > .el-tooltip > i {
    /* font-size : 35px;
    width : 34px; */
    margin-right: 0;
  }

  .nav-left .el-menu-item:focus {
    background: none;
  }

  .nav_scrollbar {
    padding: 5px 0 10px 0;
    /*padding: 20px 0 10px 10px;*/
    max-height: calc(100vh - 50px);
    box-sizing: border-box;
    margin-bottom: -10px !important;
  }
</style>
<template>
  <el-scrollbar wrap-class="nav_scrollbar" wrap-style view-style view-class="view-box" :native="false">
    <el-menu ref="menu" :collapse="isCollapse" :default-active="active" @open="handleOpen" @close="handleClose" @select="handleSelect" d-text-color="#222" text-color="#b3becd" background-color='#324157' active-text-color="#fff" d-active-text-color="#2c6eb1" unique-opened>
      <template v-for="(v,k) in data">
        <el-submenu v-if="v.child && v.childLen > 0" :index="v.index" v-bind:key="k" v-show="v.show!==false">
          <template slot="title">
            <i v-if="typeof v.icon!=='undefined'" :class="v.icon||'icon-flag'"></i> <span class="menu_title">{{ v.title }}</span>
          </template>
          <el-menu-item v-for="(vv,kk) in v.child" :key="kk" :index="vv.index" v-show="vv.show!==false">
            <i v-if="typeof vv.icon!=='undefined'" :class="vv.icon||'icon-flag'"></i> {{ vv.title }}
          </el-menu-item>
        </el-submenu>
        <el-menu-item v-else :index="v.index" v-bind:key="k" v-show="v.show!==false">
          <i v-if="v.icon" :class="v.icon"></i> <span slot="title" class="menu_title">{{ v.title }}</span>
        </el-menu-item>
      </template>
    </el-menu>
  </el-scrollbar>
</template><!--suppress VueDuplicateTag, HtmlUnknownTarget -->
<script src='script/nav.js'></script><!--suppress VueDuplicateTag -->
<script>
  var navLoadDefault,
    currentNav = [],
    verifyRouting = function (page, navs) {
      for (var i = 0, length = navs.length; i < length; i++) {
        var index = 'main/' + navs[i].index;
        var child = navs[i].child;
        if (index === page) {
          return navs[i];
        } else if (child) {
          var childIndex = verifyRouting(page, child);
          if (childIndex) return childIndex;
        }
      }
    };
  Spa.define({
    beforeDestroy: function () {
      navLoadDefault = false;
    },
    data: function () {
      return {
        __holdUpTimer: null,
        data: [],//this.initMenu()
      };
    },
    mounted: function () {
      var nav = [];
      if (navs['serve']) {// 从后台获取菜单
        nav = this.$store['getters']['menus'];
      } else {// 手动设置菜单
        nav = [].concat(
          navs['global'],
          navs['customize'][this.$store['getters'].groupID] || []
        );
      }
      this.initMenu(JSON.parse(JSON.stringify(nav)));
    },
    watch: {
      router: {
        handler: function (val) {
          if (navs['closeIntercept'] !== false && val) this.holdUp(val);
        },
        deep: true,
        immediate: true
      }
    },
    props: {
      isCollapse: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      active: function () {
        var breadcrumb = [].concat($this.$store.state.breadcrumb),
          current;
        while (breadcrumb.length >= 1) {
          current = breadcrumb.pop();
          if (current && current['show'] !== false) break;
        }
        return current ? current['index'] : this.router.page.slice(5);
      }
    },
    methods: {
      initMenu: function (nav) {
        currentNav = JSON.parse(JSON.stringify(nav));
console.log(Spa.title)
        // Spa.title = userTypes;
        this.$SpaSetTitle();

        if (Spa.debug && !navLoadDefault) {
          navLoadDefault = true;
          currentNav.push({
            title: '页面示例',
            index: 'demo',
            icon: 'icon-folder',
            // real: true,
            child: [
              { title: '默认示例', icon: '', index: 'demo/demo' },
              { title: '列表示例', icon: 'icon-list', index: 'demo/demo-lists' },
              {
                title: '列表编辑',
                icon: 'icon-edit',
                index: 'demo/demo-lists-edit'
              },
              {
                title: '表单示例',
                icon: 'icon-checkmark-square-',
                index: 'demo/demo-form'
              },
              {
                title: '动态表单',
                icon: 'icon-layout',
                index: 'demo/demo-form2'
              },
              {
                title: '标签示例',
                icon: 'icon-pricetags',
                index: 'demo/demo-tabs'
              },
              { title: '图标示例', icon: 'icon-award', index: 'demo/demo-icon' },
              {
                title: 'Markdown',
                icon: 'icon-file-remove',
                index: 'demo/demo-markdown'
              },
              {
                title: '可视化图表',
                icon: 'icon-pie-chart-',
                index: 'demo/echarts'
              },
              {
                title: '富文本示例',
                icon: 'icon-file-text',
                index: 'demo/demo-content'
              }
            ]
          });
        }
        this.$store.commit('setNav', currentNav);
        for (var i = 0, length = currentNav.length; i < length; i++) {
          var childLen = 0,
            child = currentNav[i]['child'];
          if (child && child.length > 0) {
            for (var j = 0, len = child.length; j < len; j++) {
              if (child[j].show !== false) {
                childLen++;
              }
            }
          }
          currentNav[i]['childLen'] = childLen;
        }
        this.data = currentNav;
        return currentNav;
      },
      holdUp: function (val) {
        clearTimeout(this.__holdUpTimer);
        if (navLoadDefault) {
          var verify = verifyRouting(val.page, currentNav);
          if (!verify && val.page.indexOf('main') === 0 && val.page !== 'main/main') {
            // 不在导航栏的不显示
            this.$nextTick(function () {
              Spa.replace('/not-exist');
            });
          }
        } else {
          this.__holdUpTimer = setTimeout(function () {
            $this.holdUp(val);
          }, 200);
        }
      },
      handleNav: function () {
        $this.$emit('handle');
      },
      handleOpen: function (key, keyPath) {
      },
      handleClose: function (key, keyPath) {
      },
      handleSelect: function (key, keyPath) {
        this.$go(key);
        if ($this.isCollapse) {
          setTimeout(function () {
            $this.$refs['menu'] && ($this.$refs['menu'].openedMenus = []);
          }, 300);
        }
      }
    }
  });
</script>
