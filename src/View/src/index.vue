<style>
/* .content-box {
 position: relative;
  margin: 0 20px;
  padding: 20px 0 0; 
}

.content-main {
  position: absolute;
  width: 100%;
} */
.main_scrollbar {
  min-height: calc(100vh - 60px);
}
.nav-collapse-icon i {
  padding: 2px 5px;
  position: absolute;
  z-index: 1;
  left: 0px;
  background: rgba(234, 241, 247, 0.2);
  text-align: center;
  -webkit-animation: opacity 2s infinite;
  animation: opacity 2s infinite;
}
@-webkit-keyframes opacity {
  0%,
  100% {
    opacity: 0.1;
  }
  50% {
    opacity: 0.9;
  }
}
@keyframes opacity {
  0%,
  100% {
    opacity: 0.1;
  }
  50% {
    opacity: 0.9;
  }
}
.backup_icon {
  background: #2c6eb1;
  border-radius: 100%;
  width: 30px;
  height: 30px;
  position: fixed;
  bottom: 20px;
  right: 20px;
  text-align: center;
  color: #fff;
  line-height: 30px;
  font-size: 20px;
  -webkit-animation: opacity 2s infinite;
  animation: opacity 2s infinite;
}
</style>
<template>
  <el-container id="main">
    <el-header class="header" height="60px">
      <components_nav-top aria-label="顶部导航" @handle="handleNav" @click="clickTopNav"></components_nav-top>
    </el-header>
    <el-container class="content">
      <el-aside class="nav-left" :style="asideStyle">
        <div class="nav-collapse-icon">
          <i v-if="isCollapse" @click="handleNav" class="icon-arrowhead-right-outl tap"></i>
          <i v-else @click="handleNav" class="icon-arrowhead-left-outli tap"></i>
        </div>
        <components_nav aria-label="页面导航" @handle="handleNav" :is-collapse="isCollapse"></components_nav>
      </el-aside>
      <el-container class="content-container">
        <el-main ref="content-box" class="content-box" aria-label="页面内容">
          <components_breadcrumb></components_breadcrumb>
          <!--包含list关键字的页面进行缓存-->
          <keep-alive :max="3" :include="/lists/">
            <component class="content-main" v-bind:is="childView"></component>
          </keep-alive>
        </el-main>
        <!-- <el-footer class='footer'>@jie</el-footer> -->
      </el-container>
    </el-container>
    <el-dialog
      :show-close="false"
      class="dialog-view"
      :title="editPassViewDialogtitle"
      :visible.sync="editPassViewDialogVisible"
      :close-on-press-escape="false"
      :close-on-click-modal="false"
      center
    >
      <components_edit-password @success="editPassSuccess"></components_edit-password>
    </el-dialog>
    <i v-show="scrollTopIcon" @click="goUp" class="backup_icon icon-arrow-upward tap"></i>
  </el-container>
</template>
<script>
var that, timer;
Spa.define(
  {
    data: function() {
      return {
        isCollapse: window.innerWidth <= 850,
        scrollTopIcon: false,
        editPassViewDialogtitle: "修改密码",
        editPassViewDialogVisible: false
      };
    },
    watch: {
      SpaViews: {
        handler: function(e) {
          if (e.items.length <= 1) {
            // console.log('当前组件为空，重定向到main/main');
            Spa.replace("main/main");
          } else {
            this.setBreadcrumbBr();
          }
        },
        deep: true,
        immediate: true
      },
      isCollapse: {
        handler: function() {
          this.setBreadcrumbBr();
        },
        immediate: true
      }
    },
    computed: {
      view: function() {
        return this.childView === "index" ? "main_main" : this.childView;
      },
      asideStyle: function() {
        return (this.isCollapse ? "width:84px" : "width:220px") + ";";
      }
    },
    created: function() {
      that = this;
      this.$root.getInfo();
    },
    mounted: function() {
      var resizeTag = true,
        scrollTimer = false;
      window["onresize"] = function() {
        if (resizeTag) {
          that.onResize();
          resizeTag = false;
          setTimeout(function() {
            resizeTag = true;
          }, 100);
        }
      };
      document
        .querySelector(".el-main.content-box")
        .addEventListener("scroll", function(e) {
          clearTimeout(scrollTimer);
          scrollTimer = setTimeout(function() {
            that.scrollTopIcon = e.target.scrollTop > 10;
          }, 100);
        });
      that.onResize();
    },
    methods: {
      onResize: function() {
        var clientWidth = document.documentElement.clientWidth;
        that.$root.clientWidth = clientWidth;
        if (!that.isCollapse) {
          that.isCollapse = clientWidth <= 850;
        }
        that.setBreadcrumbBr();
      },
      setBreadcrumbBr: function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
          var $box = document.querySelector(".el-main.content-box");
          var $breadcrumb = document.querySelector(".breadcrumb");
          var $viewTitleRight = document.querySelector(
            ".view-title.float-clear>.view-title-right"
          );
          if ($breadcrumb && $viewTitleRight) {
            if (
              $breadcrumb.offsetWidth + $viewTitleRight.offsetWidth >=
              $box.offsetWidth - 25
            ) {
              $viewTitleRight.classList.add("view-title-right__alone");
            } else {
              $viewTitleRight.classList.remove("view-title-right__alone");
            }
          }
          timer = false;
        }, 300);
      },
      clickTopNav: function(name) {
        console.log(name);
        switch (name) {
          case "editPassword":
            that.editPassViewDialogVisible = true;
            break;
          default:
        }
      },
      editPassSuccess: function() {
        that.editPassViewDialogVisible = false;
      },
      handleNav: function() {
        this.isCollapse = !this.isCollapse;
      },
      handleOpen: function(key, keyPath) {
        console.log(key, keyPath);
      },
      handleClose: function(key, keyPath) {
        console.log(key, keyPath);
      },
      goUp: function() {
        window['scrollSmoothTo'](that.$refs["content-box"].$el,0);
      }
    }
  },
  [
    "main/main",
    "components/nav",
    "components/nav-top",
    "components/breadcrumb",
    "components/edit-password"
  ]
);
</script>
