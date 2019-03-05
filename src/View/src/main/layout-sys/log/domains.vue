<style>
</style>
<template>
  <div>
    <div class="view-title float-clear">
      <span v-once>{{title}}</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-select size="small" v-model="current" placeholder="请选择">
              <el-option
                v-for="item in lists"
                :key="item"
                :label="item"
                :value="item">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label>
            <el-button type="info" size="small" @click="reloadDate" icon="el-icon-refresh">刷新</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel">
      <el-input
        type="textarea"
        ref="textarea"
        placeholder="请先点击右上角下拉框选择可读日志"
        autosize
        :readonly="true"
        :autosize="{ minRows: 20, maxRows: 30}"
        v-model="content">
      </el-input>
    </div>
  </div>
</template>
<script>
  var title = "域名检测日志",
    that;
  Spa.define(
    {
      mixins: [mixinLists],
      data: function () {
        return {
          title: title,
          SpaTitle: title + " - %s",
          lists: [],
          current: "",
          content: ""
        };
      },
      created: function () {
        that = this;
      },
      watch: {
        current: function (v) {
          that.getDate(v);
        }
      },
      mounted: function () {
        that.getDate();
      },
      computed: {},
      init: function (query, search) {
      },
      methods: {
        reloadDate: function () {
          that.getDate();
        },
        getDate: function (file) {
          that.$api('SysLogDomains', {name: file || that.current}).then(function (e) {
            console.log(e);
            that.lists = e.data.lists;
            that.content = e.data.content;
            that.current = e.data.current;
          }).catch(function (e) {
            console.log(e);
          }).finally(function () {
            that.$nextTick(function () {
              that.$refs.textarea.$el.querySelector('textarea').scrollTop = 0;
            });
          });
        }
      }
    },
    [],
    "/index"
  );
</script>
