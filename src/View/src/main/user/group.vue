<style>
  .group-table {
    padding-bottom: 30px;
  }

</style>
<template>
  <div>
    <div class="view-title float-clear">
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-button
              type="primary"
              size="mini"
              @click="addRowStatus"
              icon="el-icon-plus"
              :disabled="isAddRow"
            >添加
            </el-button>
            <el-button type="info" size="mini" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <fieldset>
      <legend>{{title}}</legend>
      <aside v-loading="ml_listsLoading" class="group-table">
        <el-table :data="ml_data" style="width: 100%" size="mini">
          <el-table-column prop="id" label="ID" width="50"></el-table-column>
          <el-table-column show-overflow-tooltip label="角色名称" min-width="120">
            <template slot-scope="scope">
              <div v-if="scope.row._isEdit">
                <el-input v-model="scope.row.name" placeholder="角色名称" size="mini"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.name">{{scope.row.name}}</div>
            </template>
          </el-table-column>
          <el-table-column show-overflow-tooltip label="角色简介" min-width="120">
            <template slot-scope="scope">
              <div v-if="scope.row._isEdit">
                <el-input v-model="scope.row.remark" placeholder="角色简介" size="mini"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.remark">{{scope.row.remark}}</div>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="350">
            <template slot-scope="scope">
              <div class="btns-operating">
                <el-button
                  :disabled="scope.row._isEdit"
                  @click="openRuleView(scope)"
                  slot="reference"
                  size="mini"
                  type="success"
                  title="查看规则"
                  icon="icon-person-done"
                >角色规则
                </el-button>
                <el-button
                  v-bind="getEditBtnAttrs(scope)"
                  size="mini"
                  @click="editRow(scope)"
                >{{getEditBtnAttrs(scope).title}}
                </el-button>
                <el-button
                  v-if="scope.row._isEdit"
                  title="放 弃"
                  @click="quitRow(scope)"
                  size="mini"
                  icon="el-icon-close"
                >放 弃
                </el-button>
                <template>
                  <el-popover placement="top" width="160" v-model="scope.row._isPopover">
                    <p>确定删除吗？</p>
                    <div>
                      <el-button
                        size="mini"
                        @click="scope.row._isPopover = false"
                        type="info"
                        plain
                      >取 消
                      </el-button>
                      <el-button type="danger" size="mini" @click="deleteRow(scope)" plain>确 定</el-button>
                    </div>
                    <el-button
                      v-show="!scope.row._isEdit"
                      slot="reference"
                      size="mini"
                      type="danger"
                      icon="el-icon-delete"
                      title="删 除"
                    >删除
                    </el-button>
                  </el-popover>
                </template>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </aside>
    </fieldset>

    <!-- <el-dialog
      class="dialog-view"
      title="权限编辑"
      :visible.sync="viewDialogVisible"
      :close-on-press-escape="false"
      :close-on-click-modal="false"
      center
    >
      <components_rule-view  @submit=""></components_rule-view>
    </el-dialog>-->
  </div>
</template>
<script>
  var dataFormat = { title: '', date: '', id: 0 },
    that,
    title = '角色设置';
  Spa.define(
    {
      mixins: [mixinLists],
      data: function () {
        return {
          title: title,
          SpaTitle: title + ' - %s',
          tmpData: [],
          isAddRow: false,
          viewDialogVisible: true
        };
      },
      created: function () {
        that = this;
      },
      computed: {},
      init: function (query, search) {
        that.ml_pagesize = 10;
        // debugger
      },
      mounted: function () {
      },
      methods: {
        openRuleView: function (e) {
          that.$go('user/rules/@' + e.row.id);
        },
        addRowStatus: function () {
          that.isAddRow = true;
          that.ml_data.unshift(
            Object.assign(
              { _isEdit: true, _isPopover: false, _isAdd: true },
              dataFormat
            )
          );
        },
        quitRow: function (e) {
          var index = e.$index;
          if (!e.row._isAdd) {
            that.$set(
              this.ml_data,
              e.$index,
              Object.assign({}, this.tmpData[index])
            );
          } else {
            that.isAddRow = false;
            that.ml_data.splice(e.$index, 1);
          }
        },
        deleteRow: function (e) {
          that
          .$api(apis.sysDeleteGroup, { id: e.row })
          .then(function () {
            that.ml_data.splice(e.$index, 1);
            that.ml_pagetotal--;
            that.$nextTick(function () {
              if (that.ml_data.length <= 0) that.getLists();
            });
          })
          .catch(function (e) {
            that.$warMsg(e);
          });
        },
        addRow: function (e) {
          that
          .$api(apis.sysCreateGroup, e.row)
          .then(function (v) {
            e.row._isEdit = false;
            e.row._isAdd = false;
            that.isAddRow = false;
            that.$set(that.ml_data, e.$index, Object.assign({}, e.row, v.data));
          })
          .catch(function (e) {
            that.$warMsg(e);
          });
        },
        editRow: function (e) {
          if (e.row._isAdd) {
            this.addRow(e);
            return;
          }
          if (e.row._isEdit) {
            that
            .$api(apis.sysUpdateGroup, e.row)
            .then(function (v) {
              e.row._isEdit = false;
              that.$set(
                that.ml_data,
                e.$index,
                Object.assign({}, e.row, v.data)
              );
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
          } else {
            this.$set(this.tmpData, e.$index, Object.assign({}, e.row));
            e.row._isEdit = !e.row._isEdit;
          }
        },
        getEditBtnAttrs: function (e) {
          return e.row._isEdit
            ? {
              title: '提 交',
              type: 'primary',
              icon: 'el-icon-check'
            }
            : {
              title: '编 辑',
              type: 'info',
              icon: 'el-icon-edit'
            };
        },
        getLists: function () {
          var data = { page: this.ml_page, pagesize: this.ml_pagesize };
          if (this.ml_searchKey) {
            data['key'] = this.ml_searchKey;
          }
          that.isAddRow = false;
          that.ml_listsLoading = true;
          this.$api(apis.sysGroupLists, data)
              .then(function (v) {
                that.ml_data = v.data.map(function (e) {
                  e._isEdit = false;
                  e._isPopover = false;
                  return e;
                });
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
    ['/components/rule-view'],
    '/index'
  );
</script>
