<style>
  .h5_url {
    font-size: 13px;
    border: 0;
    width: 100%;
  }

  .el-table__body .el-textarea__inner {
    padding: 5px;
  }
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span>链接管理</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-button type="primary" size="small" @click="add" icon="el-icon-plus">添加</el-button>
            <el-button type="info" size="small" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel">
      <div v-loading="ml_listsLoading">
        <el-table :data="ml_data" style="width: 100%" size="mini">
          <el-table-column prop="scene_id" label="秀ID" width="60"></el-table-column>
          <el-table-column label="标题" min-width="120">
            <template slot-scope="scope">
              <div
                class="text-nowrap"
                :title="scope.row.scenename_varchar"
              >{{ scope.row.scenename_varchar }}
              </div>
            </template>
          </el-table-column>
          <!--<el-table-column label="cnzz" width="75">-->
          <!--<template slot-scope="scope">-->
          <!--<a :href="'http://new.cnzz.com/v1/login.php?siteid='+scope.row.cnzz_id" target="_new">点击查看</a>-->
          <!--</template>-->
          <!--</el-table-column>-->
          <el-table-column label="备注" min-width="120">
            <template slot-scope="scope">
              <div v-if="scope.row.isEdit">
                <el-input type="textarea" v-model="scope.row.remark" placeholder size="small"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.remark">{{scope.row.remark}}</div>
            </template>
          </el-table-column>
          <el-table-column label="直接跳转" min-width="120">
            <template slot-scope="scope">
              <div v-if="scope.row.isEdit">
                <el-input type="textarea" v-model="scope.row.jump_url" placeholder size="small"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.jump_url">{{scope.row.jump_url}}</div>
            </template>
          </el-table-column>
          <el-table-column label="分享跳转" min-width="120">
            <template slot-scope="scope">
              <div v-if="scope.row.isEdit">
                <el-input type="textarea" v-model="scope.row.share_url" placeholder size="small"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.share_url">{{scope.row.share_url}}</div>
            </template>
          </el-table-column>
          <el-table-column label="审核状态" width="70">
            <template slot-scope="scope">
              <span v-if="scope.row.status === 0">审核中</span>
              <span v-if="scope.row.status === 1">已通过</span>
              <span class="text-nowrap" v-if="scope.row.status === 2">被驳回:{$vo.desc}</span>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="300">
            <template slot-scope="scope">
              <div class="btns-operating">
                <el-button
                  v-bind="getAttrs(scope)"
                  v-text="scope.row.isEdit?'提交':'编辑'"
                  size="mini"
                  @click="editRow(scope)"
                ></el-button>
                <el-button
                  v-if="scope.row.isEdit"
                  title="放弃"
                  @click="quitRow(scope)"
                  size="mini"
                  icon="el-icon-close"
                ></el-button>
                <template v-if="!scope.row.isEdit">
                  <el-button size="mini" @click="seeLink(scope)" type="primary" title="查看链接">查看</el-button>
                  <el-button
                    size="mini"
                    @click="reloadUrlContent(scope)"
                    type="success"
                    title="更新内容"
                  >更新
                  </el-button>
                  <el-popover placement="top" width="160" v-model="scope.row.popover">
                    <p>确定删除吗？</p>
                    <div>
                      <el-button size="mini" @click="scope.row.popover = false" type="info" plain>取消</el-button>
                      <el-button type="danger" size="mini" @click="deleteRow(scope)" plain>确定</el-button>
                    </div>
                    <el-button
                      slot="reference"
                      size="mini"
                      type="danger"
                      icon="el-icon-delete"
                      title="删除"
                    ></el-button>
                  </el-popover>
                </template>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="tip-page" v-if="!!ml_pagetotal">
        <el-pagination
          :current-page.sync="ml_page"
          @size-change="ml_sizeChange"
          @current-change="ml_currentChange"
          background
          layout="prev, pager, next, sizes"
          :total="ml_pagetotal"
          :page-size.sync="ml_pagesize"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
  var that;
  Spa.define(
    {
      mixins: [mixinLists],
      data: function () {
        return {
          SpaTitle: "链接列表 - %s",
          tmpData: {},
          domain: ""
        };
      },
      created: function () {
        that = this;
      },
      computed: {},
      init: function (query, search) {
        that.ml_pagesize = 10;
      },
      methods: {
        seeLink: function (e) {
          var url = createUrl(e.row.urls, that.domain);
          that.$confirm(
            '<input class="text h5_url" value="' + url + '">',
            "查看链接",
            {
              confirmButtonText: "复 制",
              cancelButtonText: "关 闭",
              center: true,
              dangerouslyUseHTMLString: true,
              beforeClose: function (e, a, done) {
                if (e === "confirm") {
                  document.querySelector(".h5_url.text").select();
                  document.execCommand("Copy");
                  that.$sucMsg("复制成功!");
                }
                done();
              }
            }
          );
        },
        reloadUrlContent: function (e) {
          var id = e.row.id;
          that
            .$api("reloadUrlContent", {id: id})
            .then(function (v) {
              that.$sucMsg(v.msg);
              that.ml_reloadLists();
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
        },
        getAttrs: function (e) {
          return e.row.isEdit
            ? {
              title: "提交",
              type: "primary"
            }
            : {
              title: "编辑",
              type: "info"
            };
        },
        quitRow: function (e) {
          var index = e.$index;
          this.$set(
            this.ml_data,
            e.$index,
            Object.assign({}, this.tmpData[index])
          );
        },
        deleteRow: function (e) {
          that
            .$api("ListsDelete", e.row)
            .then(function (v) {
              that.ml_reloadLists();
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
        },
        editRow: function (e) {
          var isEdit = e.row.isEdit;
          if (isEdit) {
            that
              .$api("userLinksEdit", e.row)
              .then(function (v) {
                var data = v.data;
                data.isEdit = false;
                that.$set(that.ml_data, e.$index, Object.assign({}, e.row, data));
              })
              .catch(function (e) {
                that.$warMsg(e);
              });
          } else {
            this.$set(this.tmpData, e.$index, Object.assign({}, e.row));
            e.row.isEdit = !isEdit;
          }
        },
        add: function () {
          that.$go("./edit");
        },
        getLists: function () {
          var data = {page: this.ml_page, pagesize: this.ml_pagesize};

          that.ml_listsLoading = true;

          this.$api("userLinks", data)
            .then(function (e) {
              var data = e.data.items;
              data.map(function (e) {
                e.isEdit = false;
                e.popover = false;
                return e;
              });
              var page = e.data.page;
              that.domain = e.data.domain;
              that.ml_getLists(data, page);
            })
            .catch(function (e) {
              that.$warMsg(e);
            })
            .finally(function () {
              that.ml_listsLoading = false;
            });
        }
      }
    },
    [],
    "/index"
  );
</script>
