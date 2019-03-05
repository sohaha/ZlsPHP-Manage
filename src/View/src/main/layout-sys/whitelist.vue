<style></style>
<template>
  <div>
    <div class="view-title float-clear">
      <span>域名白名单</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-button
              type="primary"
              size="small"
              @click="addRowStatus"
              icon="el-icon-plus"
              :disabled="isAddRow"
            >添加</el-button>
            <el-button type="info" size="small" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel">
      <div v-loading="ml_listsLoading">
        <el-table
          @selection-change="handleSelectionChange"
          :data="ml_data"
          style="width: 100%"
          size="mini"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <!--<el-table-column prop="id" label="#" width='80'></el-table-column>-->
          <el-table-column label="域名">
            <template slot-scope="scope">
              <div v-if="scope.row._isEdit">
                <el-input v-model="scope.row.content" placeholder="不包含http://" size="small"></el-input>
              </div>
              <div v-else class="text-nowrap" :title="scope.row.content">{{scope.row.content}}</div>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="180">
            <template slot-scope="scope">
              <div class="btns-operating">
                <el-button v-bind="getEditBtnAttrs(scope)" size="mini" @click="editRow(scope)"></el-button>
                <el-button
                  v-if="scope.row._isEdit"
                  title="放 弃"
                  @click="quitRow(scope)"
                  size="mini"
                  icon="el-icon-close"
                ></el-button>
                <template v-else>
                  <el-popover placement="top" width="160" v-model="scope.row._isPopover">
                    <p>确定删除吗？</p>
                    <div>
                      <el-button
                        size="mini"
                        @click="scope.row._isPopover = false"
                        type="info"
                        plain
                      >取 消</el-button>
                      <el-button type="danger" size="mini" @click="deleteRow(scope)" plain>确 定</el-button>
                    </div>
                    <el-button
                      slot="reference"
                      size="mini"
                      type="danger"
                      icon="el-icon-delete"
                      title="删 除"
                    ></el-button>
                  </el-popover>
                </template>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="tip-page" v-if="!!ml_pagetotal">
        <div class="panel-left" v-show="showColumnBtn">
          <el-button
            @click="deleteSelection"
            size="mini"
            type="danger"
            icon="el-icon-delete"
            title="删除选中"
          ></el-button>
        </div>
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
var that,
  dataFormat = { id: "", content: "" };
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        SpaTitle: "域名白名单管理 - %s",
        tmpData: [],
        selectIds: [],
        isAddRow: false
      };
    },
    created: function() {
      that = this;
    },
    computed: {
      showColumnBtn: function() {
        return that.selectIds.length > 0;
      }
    },
    init: function(query, search) {
      that.ml_pagesize = 10;
    },
    watch: {},
    methods: {
      handleSelectionChange: function(e) {
        that.selectIds = e.map(function(e) {
          return e.id;
        });
      },
      deleteSelection: function() {
        that
          .$api("DeleteUrlWhite", { id: that.selectIds })
          .then(function() {
            that.getLists();
          })
          .catch(function(e) {
            that.$warMsg(e);
          });
      },
      addRowStatus: function() {
        that.isAddRow = true;
        that.ml_data.unshift(
          Object.assign(
            { _isEdit: true, _isPopover: false, _isAdd: true },
            dataFormat
          )
        );
      },
      quitRow: function(e) {
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
      deleteRow: function(e) {
        that
          .$api("DeleteUrlWhite", e.row)
          .then(function() {
            that.ml_data.splice(e.$index, 1);
            that.$nextTick(function() {
              if (that.ml_data.length <= 0) that.getLists();
            });
          })
          .catch(function(e) {
            that.$warMsg(e);
          });
      },
      addRow: function(e) {
        that
          .$api("EditUrlWhiteLists", e.row)
          .then(function(v) {
            e.row._isEdit = false;
            e.row._isAdd = false;
            that.isAddRow = false;
            that.$set(that.ml_data, e.$index, Object.assign({}, e.row, v.data));
          })
          .catch(function(e) {
            that.$warMsg(e);
          });
      },
      editRow: function(e) {
        if (e.row._isAdd) {
          this.addRow(e);
          return;
        }
        if (e.row._isEdit) {
          that
            .$api("EditUrlWhiteLists", e.row)
            .then(function(v) {
              e.row._isEdit = false;
              that.$set(
                that.ml_data,
                e.$index,
                Object.assign({}, e.row, v.data)
              );
            })
            .catch(function(e) {
              that.$warMsg(e);
            });
        } else {
          this.$set(this.tmpData, e.$index, Object.assign({}, e.row));
          e.row._isEdit = !e.row._isEdit;
        }
      },
      getEditBtnAttrs: function(e) {
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
      getLists: function() {
        var data = { page: this.ml_page, pagesize: this.ml_pagesize };
        if (this.ml_searchKey) {
          data["key"] = this.ml_searchKey;
        }
        that.isAddRow = false;
        that.ml_listsLoading = true;
        this.$api("getUrlWhiteLists", data)
          .then(function(v) {
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
  [],
  "/index"
);
</script>
