<style>
  .page-sys-logs .view-title-right {
    position: absolute;
    right: 0;
    bottom: -45px;
    z-index: 1;
  }

  .page-sys-logs .el-form-item {
    max-width: 120px;
  }
</style>
<template>
  <div class='page-sys-logs'>
    <div class="view-title float-clear no">
      <span>{{title}}</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-select clearable size="small" v-model="logType" placeholder="请选择状态">
              <el-option
                v-for="item in logTypes"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="info" size="small" @click="ml_reloadLists" icon="el-icon-refresh">
              刷新
            </el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <el-tab-pane :key="k" v-for="(v,k) in tabs" :label="v" :name="k">
      </el-tab-pane>
    </el-tabs>
    <div class="panel">
      <div v-loading="ml_listsLoading">
        <el-table @selection-change="handleSelectionChange" :data="ml_data" style="width: 100%" size="mini">
          <el-table-column :selectable="isUnreadMessageTab" type="selection" width="55"></el-table-column>
          <el-table-column prop="create_time" label="日期" width="150">
          </el-table-column>
          <el-table-column label="标题">
            <template slot-scope="scope">
              <div class="text-nowrap" :title="scope.row.content">
                {{ scope.row.title }}
              </div>
            </template>
          </el-table-column>
          <el-table-column label="类型" width="100">
            <template slot-scope="scope">
              <el-tag v-bind="getTagAttrs(scope.row.type)" v-text='getTagAttrs(scope.row.type).title'>
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作">
            <template slot-scope="scope">
              <div class="text-nowrap">
                <el-button type="primary" v-if="isUnreadMessageTab(scope.row)" title="标记已读" size="mini" @click='readSelection(scope.row.id)'>标记已读</el-button>
                <span v-else> -- </span>
              </div>
            </template>
          </el-table-column>
        </el-table>
        <div class="tip-page" v-if="!!ml_pagetotal">
          <div class="panel-left" v-show="showColumnBtn">
            <el-button @click="readSelection()" size="mini" type="primary" icon="icon-inbox" title="标记已读"></el-button>
          </div>
          <el-pagination :current-page.sync="ml_page" @size-change="ml_sizeChange" @current-change="ml_currentChange" background layout="prev, pager, next, sizes" :total="ml_pagetotal" :page-size.sync="ml_pagesize">
          </el-pagination>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
  var that, title = '日志列表';
  Spa.define({
    mixins: [mixinLists],
    data: function () {
      return {
        title: title,
        SpaTitle: title + ' - %s',
        activeName: 'unreadMessage',
        tabs: {
          unreadMessage: '未读消息',
          allMessage: '全部消息',
        },
        logType: '',
        logTypes: [
          { label: '普通日志', value: 1, type: '' },
          { label: '警告日志', value: 2, type: 'warning' },
          { label: '错误日志', value: 3, type: 'danger' },
        ],
        selectIds: [],
      };
    },
    created: function () {
      that = this;
    },
    watch: {
      logType: function () {
        that.ml_reloadLists();
      }
    },
    computed: {
      showColumnBtn: function () {
        return that.selectIds.length > 0;
      },
    },
    init: function (query, search) {
      that.ml_reloadLists();
    },
    methods: {
      getTagAttrs: function (i) {
        for (let j = 0, length = that.logTypes.length; j < length; j++) {
          if (that.logTypes[j]['value'] === i) {
            return { title: that.logTypes[j].label, type: that.logTypes[j].type };
          }
        }
        return {};
        // <el-tag type="success" size="mini" v-if="scope.row.type===1">
        //     正常
        //     </el-tag>
        //     <el-tag type="warning" size="mini" v-if="scope.row.type===2">
        //     警告
        //     </el-tag>
        //     <el-tag type="danger" size="mini" v-if="scope.row.type===3">
        //     错误
        //     </el-tag>
        //     <el-tag type="danger" size="mini" v-if="scope.row.type===4">
        //     通知
        //     </el-tag>
      },
      readSelection: function (e) {
        console.log(e, that.selectIds);
        var data = { ids: [] };
        if (!e) {
          data.ids = that.selectIds;
        } else {
          data.ids = [e];
        }
        that.$api(apis.sysUpdateMessageStatus, data)
            .then(function (e) {
              window['SysGetUnreadMessageCount'] && window['SysGetUnreadMessageCount']();
              that.ml_reloadLists();
            })
            .catch(function (e) {
              that.$warMsg(e);
            });
      },
      handleSelectionChange: function (e) {
        that.selectIds = e.map(function (e) {
          return e.id;
        });
      },
      isUnreadMessageTab: function (row) {
        return row.status === 1;
      },
      handleClick: function (tab, event) {
        that.ml_page = 1;
        that.$nextTick(function () {
          that.getLists();
        });
      },
      getLists: function () {
        if (that.ml_listsLoading) {
          return;
        }
        var data = {
          page: this.ml_page,
          pagesize: this.ml_pagesize,
          unread: +(this.activeName === 'unreadMessage'),
          type: this.logType
        };
        that.ml_listsLoading = true;
        that.$api(apis.sysUserLogs, data)
            .then(function (e) {
              that.ml_getLists(e.data.items, e.data.page);
            })
            .catch(function (e) {
              that.$warMsg(e);
            })
            .finally(function () {
              // that.$store.commit('setUnreadMessageCount', 0);
              that.ml_listsLoading = false;
            });
      },
    },
  }, [], '/index');
</script>
