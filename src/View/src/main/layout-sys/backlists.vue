<style>
  .backview .popover-form .el-form-item {
    margin-bottom: 0;
  }
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span>返回链接管理</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-select size="small" v-model="stateType" placeholder="请选择状态">
              <el-option
                v-for="item in stateTypes"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-popover
              popper-class="backview"
              v-model="visible"
              title="全局返回链接"
              placement="left-start"
              width="400"
              trigger="click"
            >
              <el-form
                @submit.prevent.stop.native
                class="popover-form"
                label-position="top"
                ref="ruleForm"
                label-width="110px"
              >
                <el-form-item label prop="mainfh">
                  <el-input
                    size="small"
                    @keyup.enter.stop.prevent.native
                    v-model="globalFh"
                    placeholder="http://"
                  ></el-input>
                </el-form-item>
                <el-form-item label>
                  <el-switch
                    v-model="globalFhType"
                    active-text="自动追加用户ID"
                    inactive-text="">
                  </el-switch>
                </el-form-item>
                <el-form-item label>
                  <el-button class="block" type="primary" size="small" @click="onSaveGlobal">保 存</el-button>
                </el-form-item>
              </el-form>
              <el-button
                slot="reference"
                type="warning"
                size="small"
                icon="icon-shake"
                :disabled="isAddRow"
              >全局
              </el-button>
            </el-popover>
            <el-button
              type="success"
              size="small"
              @click="goCustomize"
              icon="icon-shopping-bag"
              :disabled="isAddRow"
            >自定义
            </el-button>
            <el-button type="info" size="small" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel" v-loading="ml_listsLoading">
      <div>
        <el-table
          @sort-change="onSortChange"
          @selection-change="handleSelectionChange"
          :data="ml_data"
          style="width: 100%"
          size="mini"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="scene_id" label="秀ID" width="90"></el-table-column>
          <el-table-column label="用户" min-width="120">
            <template slot-scope="scope">
              <div class="text-nowrap text" :title="scope.row.uname">{{scope.row.uname}}</div>
            </template>
          </el-table-column>
          <el-table-column prop="code" label="CODE" min-width="120">
            <template slot-scope="scope">
              <div class="text-nowrap text" :title="scope.row.code">{{scope.row.code}}</div>
            </template>
          </el-table-column>
          <el-table-column
            label="状态"
            width="90"
            prop="isback"
            :sortable="stateType===-1?'custom':false"
          >
            <template slot-scope="scope">
              <el-tag size="mini" v-if="scope.row.isback" type="success">已开启</el-tag>
              <el-tag size="mini" v-else type="warning">关闭中</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="180">
            <template slot-scope="scope">
              <div class="btns-operating">
                <el-button
                  v-if="scope.row.isback"
                  title="关闭"
                  @click="backUrlSwitch(0,scope.row.id)"
                  size="mini"
                  icon="icon-lock"
                ></el-button>
                <el-button
                  v-else
                  title="开启"
                  @click="backUrlSwitch(1,scope.row.id)"
                  size="mini"
                  icon="icon-unlock"
                ></el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="tip-page" v-if="!!ml_pagetotal">
        <div class="panel-left" v-show="showColumnBtn">
          <el-button
            @click="backUrlSwitch(1)"
            size="mini"
            type="success"
            icon="icon-unlock"
            title="开启选中"
          ></el-button>
          <el-button
            @click="backUrlSwitch(0)"
            size="mini"
            type="warning"
            icon="icon-lock"
            title="关闭选中"
          ></el-button>
        </div>
        <el-pagination
          :current-page.sync="ml_page"
          @size-change="ml_sizeChange"
          @current-change="ml_currentChange"
          background
          layout="prev, pager, next, sizes,total"
          :page-sizes="[10, 20, 100, 1000, 10000]"
          :total="ml_pagetotal"
          :page-size.sync="ml_pagesize"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>
<script>
  var that,
    dataFormat = {title: "", date: "", id: 0};
  var isAddition = function (url) {
    var i = url.indexOf("____="), has = false;
    if (i >= 0) {
      url = url.substring(0, i);
      has = true;
    }
    return [url, has];
  };
  Spa.define(
    {
      mixins: [mixinLists],
      data: function () {
        return {
          SpaTitle: "返回链接管理 - %s",
          stateTypes: [
            {label: "全部状态", value: -1},
            {label: "已开启", value: 1},
            {label: "关闭中", value: 0}
          ],
          tmpData: [],
          selectIds: [],
          isAddRow: false,
          stateType: -1,
          stateOrder: "",
          visible: false,
          globalFh: "",
          globalFhType: false
        };
      },
      created: function () {
        that = this;
      },
      computed: {
        showColumnBtn: function () {
          return that.selectIds.length > 0;
        }
      },
      init: function (query, search) {
        that.ml_pagesize = 10;
      },
      watch: {
        stateType: function () {
          that.ml_reloadLists();
        }
      },
      mounted: function () {
        that
          .$api("SysGetGlobalBackUrl")
          .then(function (e) {
            var d = isAddition(e.data);
            that.globalFh = d[0];
            that.globalFhType = d[1];
          })
          .catch(function (e) {
            that.$warMsg(e);
          });
      },
      methods: {
        showUpdate: function () {
          that.visible = true;
        },
        onSaveGlobal: function () {
          that
            .$api("SysEditGlobalBackUrl", {url: that.globalFh, addition: +that.globalFhType})
            .then(function (e) {
              that.$sucMsg("更新成功");
              var d = isAddition(e.data);
              that.globalFh = d[0];
              that.globalFhType = d[1];
              that.visible = false;
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
        },
        goCustomize: function () {
          that.$go("layout-sys/customize-back");
        },
        onSortChange: function (e) {
          that.stateOrder = e.order ? (e.order === "descending" ? 1 : 2) : 0;
          that.ml_reloadLists();
        },
        handleSelectionChange: function (e) {
          that.selectIds = e.map(function (e) {
            return e.id;
          });
        },
        backUrlSwitch: function (status, id) {
          that.ml_listsLoading = true;
          that
            .$api("SysBackUrlSwitch", {
              id: typeof id !== "undefined" ? id : that.selectIds,
              status: status
            })
            .then(function () {
              that.ml_reloadLists();
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
        },
        deleteSelection: function () {
          console.log("删除选中", that.selectIds);
        },
        addRowStatus: function () {
          that.isAddRow = true;
          that.ml_data.unshift(
            Object.assign(
              {_isEdit: true, _isPopover: false, _isAdd: true},
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
            .$api("", e.row)
            .then(function () {
              that.ml_data.splice(e.$index, 1);
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
            .$api("", e.row)
            .then(function (v) {
              // todo test 接口地址空是不会真实发起请求所以这里 e 是不会有数据
              v = {data: {}};
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
              .$api("", e.row)
              .then(function (v) {
                // todo test 接口地址空是不会真实发起请求所以这里 e 是不会有数据
                v = {data: {}};
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
              title: "提 交",
              type: "primary",
              icon: "el-icon-check"
            }
            : {
              title: "编 辑",
              type: "info",
              icon: "el-icon-edit"
            };
        },
        getLists: function () {
          var data = {page: this.ml_page, pagesize: this.ml_pagesize};
          data["statetype"] = this.stateType;
          data["order"] = this.stateOrder;
          that.isAddRow = false;
          that.ml_listsLoading = true;
          this.$api("SysBackUrlLists", data)
            .then(function (v) {
              var data = v.data.items;
              data.map(function (e) {
                e._isEdit = false;
                e._isPopover = false;
                return e;
              });
              var page = v.data.page;
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
