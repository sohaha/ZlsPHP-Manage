<style>
.breadcrumb {
  position : absolute;
  z-index : 1;
  top : 22px;
  left : 5px;
  font-size : 14px;
}

.breadcrumb__link .el-breadcrumb__inner {
  color : #2A6CB1;
  cursor : pointer;
}

</style>
<template>
  <el-breadcrumb class="breadcrumb" separator-class="el-icon-arrow-right">
    <el-breadcrumb-item
      v-for="(v,k) in breadcrumb"
      :key="k"
      :class="breadcrumbClass(v)"
      @click.native="go(v.index)"
    >{{v.title}}</el-breadcrumb-item>
  </el-breadcrumb>
</template>
<!--suppress JSDuplicatedDeclaration -->
<script>
var that,
  getParent = function(navs, current, parent, lastNav) {
    for (var k = 0, l = navs.length; k < l; k++) {
      var nav = navs[k],
        child = nav["child"],
        path = "main/" + nav["index"];
      if (path === current) {
        if (lastNav && lastNav.length > 0) {
          for (var i = 0, ll = lastNav.length; i < ll; i++) {
            parent.push(lastNav[i]);
          }
        }
        parent.push(nav);
        return parent;
      } else if (child && child.length > 0) {
        var child = getParent(
          child,
          current,
          parent,
          [].concat(lastNav || [], [
            { title: nav.title, index: nav.real ? nav.index : "" }
          ])
        );
        if (child) {
          return child;
        }
      }
    }
  };
Spa.define({
  props: {},
  data: function() {
    return {};
  },
  created: function() {
    that = this;
  },
  mounted: function() {},
  computed: {
    breadcrumb: function() {
      var navs = that.$store.state.nav || [],
        current = that.router.page,
        defaultBreadcrumb = { title: "后台中心", index: "main" };
      return current !== "main/main"
        ? getParent(navs, current, [defaultBreadcrumb])
        : [defaultBreadcrumb];
    }
  },
  watch: {},
  init: function(query, search) {},
  methods: {
    breadcrumbClass: function(v) {
      return !!v.index ? "breadcrumb__link" : "";
    },
    go: function(e) {
      console.log(e);
      if (e && "main/" + e !== that.router.page) that.$go(e);
    }
  }
});
</script>
