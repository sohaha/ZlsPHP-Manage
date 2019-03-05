<template>
  <div>
    <div class="view-title float-clear">
      <span>查看统计</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-date-picker
              size="small"
              v-model="inquiryDate"
              type="daterange"
              align="right"
              value-format="timestamp"
              unlink-panels
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :picker-options="pickerOptions"
            ></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="info" size="small" @click="ml_reloadLists" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel">
      <div v-loading="ml_listsLoading">
        <el-table
          show-summary
          :summary-method="getSummaries"
          :data="ml_data"
          style="width: 100%"
          size="mini"
          :default-sort="{prop: 'statistics', order: 'descending'}"
        >
          <el-table-column prop="scene_id" label="秀ID" width="80"></el-table-column>
          <el-table-column label="标题" min-width="200">
            <template slot-scope="scope">
              <div
                class="text-nowrap"
                :title="scope.row.scenename_varchar"
              >{{ scope.row.scenename_varchar }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="statistics" label="流量"></el-table-column>
        </el-table>
      </div>
      <!--<div class="tip-page" v-if="!!ml_pagetotal">-->
      <!--<el-pagination :current-page.sync="ml_page" @size-change="ml_sizeChange" @current-change="ml_currentChange" background layout="prev, pager, next, sizes" :total="ml_pagetotal" :page-size.sync="ml_pagesize">-->
      <!--</el-pagination>-->
      <!--</div>-->
    </div>
  </div>
</template>
<script>
var that;
var now = +new Date();
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        SpaTitle: "查看统计 - %s",
        inquiryDate: [now, now],
        pickerOptions: {
          shortcuts: [
            {
              text: "最近一周",
              onClick: function(picker) {
                var end = new Date();
                var start = new Date();
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                picker.$emit("pick", [start, end]);
              }
            },
            {
              text: "最近一个月",
              onClick: function(picker) {
                var end = new Date();
                var start = new Date();
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                picker.$emit("pick", [start, end]);
              }
            },
            {
              text: "最近三个月",
              onClick: function(picker) {
                var end = new Date();
                var start = new Date();
                start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                picker.$emit("pick", [start, end]);
              }
            }
          ]
        }
      };
    },
    created: function() {
      that = this;
    },
    computed: {},
    init: function(query, search) {
      that.ml_pagesize = 1000000000000;
    },
    watch: {
      inquiryDate: function(v) {
        that.getLists();
      }
    },
    methods: {
      getSummaries: function(param) {
        var columns = param.columns,
          data = param.data;
        var sums = [];
        columns.forEach(function(column, index) {
          if (index === 0) {
            sums[index] = "总计";
            return;
          }
          var values = data.map(function(item) {
            return Number(item[column.property]);
          });
          if (
            !values.every(function(value) {
              return isNaN(value);
            })
          ) {
            sums[index] = values.reduce(function(prev, curr) {
              var value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
            sums[index] += " ";
          } else {
            sums[index] = "";
          }
        });
        return sums;
      },
      getLists: function() {
        var data = { page: this.ml_page, pagesize: this.ml_pagesize };
        if (that.inquiryDate && that.inquiryDate.length > 0) {
          data.startDate = Math.round(that.inquiryDate[0] / 1000);
          data.endDate = Math.round(that.inquiryDate[1] / 1000);
        }
        that.ml_listsLoading = true;

        this.$api("userCounts", data)
          .then(function(e) {
            var data = e.data.items;
            var page = e.data.page;
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
