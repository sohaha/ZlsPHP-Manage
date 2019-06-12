<style>
.page-user-menu .tree-placeholder {
  display: inline-block;
  width: 20px;
  line-height: 20px;
  height: 20px;
  text-align: center;
  margin-right: 3px;
}
</style>
<template>
  <div class="page-user-menu">
    <div class="view-title float-clear">
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-button type="primary" size="mini" icon="el-icon-plus">添加</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <fieldset>
      <legend v-once v-text="title"></legend>
      <aside class="center" :aria-label="title">
        <el-table
          :indent="0"
          :tree-props="{children: 'child'}"
          default-expand-all
          row-key="id"
          :data="ml_data"
          style="width: 100%"
          size="mini"
        >
          <el-table-column label="名称" min-width="150">
            <template slot-scope="scope">
              <div v-if="!scope.row.child||scope.row.child.length<=0" class="tree-placeholder"></div>
              <span>
                <i :class="scope.row.icon"></i>
                {{scope.row.title}}
              </span>
            </template>
          </el-table-column>
          <el-table-column prop="index" label="路径" show-overflow-tooltip max-width="200"></el-table-column>
          <el-table-column prop="date" label="类型"></el-table-column>
          <el-table-column prop="date" label="更新"></el-table-column>
          <el-table-column label="操作" width="200">
            <template slot-scope="scope">{{scope.row.__||'--'}}</template>
          </el-table-column>
        </el-table>
        <!-- <el-tree default-expand-all show-checkbox :data="data" :props="defaultProps">
          <span class="custom-tree-node" slot-scope="{ node, data }">
            <span>
              {{ node.label }}
              {{data.id}}
            </span>
            <span>
              <el-button type="text" size="mini">添加</el-button>
              <el-button type="text" size="mini">删除</el-button>
            </span>
          </span>
        </el-tree>-->
      </aside>
    </fieldset>
  </div>
</template>
<script>
var title = "菜单设置";
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
    methods: {
      getLists: function() {
        var data = { page: this.ml_page, pagesize: this.ml_pagesize };
        if (this.ml_searchKey) {
          data["key"] = this.ml_searchKey;
        }
        that.ml_listsLoading = true;
        this.$api(undefined, data)
          .then(function(v) {
            // todo test 接口地址undefined是不会真实发起请求所以这里 v 是不会有数据
            v = {
              data: {
                items: that.$store.getters.menus,
                page: { total: 40 }
              }
            };
            // test end
            var data = v.data.items;
            data.map(function(e) {
              e._isEdit = false;
              e._isPopover = false;
              return e;
            });
            var page = v.data.page;
            that.ml_getLists(data, page);
          })
          .catch(function(e) {
            that.$warMsg(e);
          })
          .finally(function() {
            that.ml_listsLoading = false;
          });
      }
    }
  },
  ["/components/demo"],
  "/index"
);
</script>
