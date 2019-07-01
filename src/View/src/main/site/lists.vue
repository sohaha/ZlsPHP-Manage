<style></style>
<template>
  <div>
    <div class="view-title float-clear">
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-button type="primary" size="mini" @click="goCreate()" icon="el-icon-plus">添加</el-button>
            <el-button type="info" size="mini" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="tip-area">温馨提示: 登录账号为站点文件夹</div>
    <fieldset>
      <legend>{{title}}</legend>
      <aside v-loading="ml_listsLoading">
        <el-table :data="ml_data" style="width: 100%" size="mini">
          <el-table-column prop="title" label="标题" max-width="180"></el-table-column>
          <el-table-column show-overflow-tooltip label="文件夹" max-width="250">
            <template slot-scope="scope">
              <div>{{ scope.row.path || ' - ' }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="password" label="密码"></el-table-column>
          <el-table-column prop="update_time" label="更新时间" width="150"></el-table-column>
          <el-table-column label="操作" width="280">
            <template slot-scope="scope">
              <div class="btns-operating">
                <el-button
                  icon="icon-attach-outline"
                  size="mini"
                  type="success"
                  underline
                  @click="preview(scope.row)"
                >预览</el-button>
                <!-- <el-button icon="icon-copy" size="mini" type="info">拷贝</el-button> -->
                <el-popover placement="top" max-width="260" v-model="scope.row.stateTip">
                  <p>确定删除 “{{scope.row.title}}” 吗？</p>
                  <div>
                    <el-button size="mini" @click="scope.row.stateTip = false" type="info" plain>取 消</el-button>
                    <el-button
                      @click="deleteSites(scope.row.id)"
                      type="danger"
                      size="mini"
                      plain
                    >确 定</el-button>
                  </div>
                  <el-button
                    slot="reference"
                    icon="el-icon-delete"
                    size="mini"
                    type="danger"
                    underline
                    click1="goCreate(scope.row.id)"
                  >删除</el-button>
                </el-popover>
                <el-button
                  icon="icon-edit-"
                  size="mini"
                  type="primary"
                  underline
                  @click="goCreate(scope.row.id)"
                >管理</el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
        <div class="tip-page" v-if="!!ml_pagetotal">
          <el-pagination
            :current-page.sync="ml_page"
            @size-change="ml_sizeChange"
            @current-change="ml_currentChange"
            background
            layout="prev, pager, next, sizes, total"
            :total="ml_pagetotal"
            :page-size.sync="ml_pagesize"
          ></el-pagination>
        </div>
      </aside>
    </fieldset>
  </div>
</template>
<script>
var loadJs = ["//cdn.jsdelivr.net/npm/zls-manage/qriously/qriously.js"];
var that;
Spa.define(
  {
    name: "site",
    mixins: [mixinLists, initTitle],
    data: function() {
      return {
        host: ""
      };
    },
    beforeCreate: function() {
      that = this;
    },
    computed: {},
    init: function(query, search) {
      console.log("init");

      that.ml_pagesize = 10;
    },
    activated: function() {
      console.log(app.SpaViews.items, this.router);

      if (this.ml_data) {
        this.ml_reloadLists();
      }
    },
    mounted: function() {},
    methods: {
      preview: function(e) {
        var html =
          '<span class="text">' + location.origin + e.fullpath + "/</span>";
        console.log(e.path);

        this.$confirm(html, "查看", {
          // confirmButtonText: "全部复制",
          // cancelButtonText: "关 闭",
          center: true,
          showConfirmButton: false,
          showCancelButton: false,
          dangerouslyUseHTMLString: true
        }).catch(function() {});
      },
      goCreate: function(id) {
        this.$go("../info" + (id ? "?id=" + id : ""));
      },
      deleteSites: function(id) {
        this.$api(apis.deleteSites, { id: id })
          .then(function(v) {
            that.ml_reloadLists();
          })
          .catch(function() {
            that.$warMsg(e);
          });
      },
      getLists: function() {
        var data = { page: this.ml_page, pagesize: this.ml_pagesize };
        if (this.ml_searchKey) {
          data["key"] = this.ml_searchKey;
        }
        that.ml_listsLoading = true;
        this.$api(apis.sites, data)
          .then(function(v) {
            var data = v.data.items || [];
            data.map(function(e) {
              e._isEdit = false;
              e._isPopover = false;
              return e;
            });
            var page = v.data.page;
            that.host = v.data["url"];
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
  [],
  "/index"
);
</script>
