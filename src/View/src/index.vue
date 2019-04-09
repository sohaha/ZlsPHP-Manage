<style>
</style>
<template>
  <el-container id="main">
    <el-header class="header" height="60px">
      <components_nav-top @handle="handleNav" @click="clickTopNav"></components_nav-top>
    </el-header>
    <el-container class="content">
      <el-aside class="nav-left" :style="asideStyle">
        <components_nav :is-collapse="isCollapse"></components_nav>
      </el-aside>
      <el-container class="content-container">
        <el-main class="content-box">
          <components_breadcrumb></components_breadcrumb>
          <!--包含list关键字的页面进行缓存-->
          <keep-alive :max="3" :include="/lists/">
            <component v-bind:is="childView"></component>
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
  </el-container>
</template>
<script>
var that, timer;
Spa.define(
  {
    data: function() {
      return {
        isCollapse: window.innerWidth <= 850,
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
      var resizeTag = true;
      window["onresize"] = function() {
        if (resizeTag) {
          that.onResize();
          resizeTag = false;
          setTimeout(function() {
            resizeTag = true;
          }, 100);
        }
      };
      that.onResize();
    },
    methods: {
      onResize: function() {
        var clientWidth = document.documentElement.clientWidth;
        that.$root.clientWidth = clientWidth;
        that.isCollapse = clientWidth <= 850;
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
