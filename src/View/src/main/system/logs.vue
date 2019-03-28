<style>
.logs-view .panel {
  padding-top: 10px;
}

.logs-view .el-switch {
  margin-bottom: 10px;
  float: right;
}

.logs-view .el-textarea__inner {
  padding: 10px;
}
</style>
<template>
  <div class="logs-view">
    <div class="view-title float-clear">
      <span v-once>{{title}}</span>
      <div class="view-title-right">
        <el-form @submit.prevent.stop.native inline class="tip-top">
          <el-form-item>
            <el-select size="small" v-model="currentType" placeholder="请选择类型">
              <el-option v-for="item in types" :key="item" :label="item" :value="item"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-select :disabled="ban" size="small" v-model="current" placeholder="请选择日志文件">
              <el-option v-for="item in lists" :key="item" :label="item" :value="item"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label>
            <el-button type="info" size="small" @click="reloadDate" icon="el-icon-refresh">刷新</el-button>
            <el-popover placement="top" width="160" v-model="stateTip">
              <p>
                确定删除吗？
                <br>（操作无法逆转）
              </p>
              <div>
                <el-button size="mini" @click="stateTip = false" type="info" plain>取 消</el-button>
                <el-button @click="deleteLog" type="danger" size="mini" plain>确 定</el-button>
              </div>
              <el-button
                :disabled="stateDel"
                slot="reference"
                size="small"
                type="warning"
                icon="el-icon-delete"
                title="删 除"
              >删 除</el-button>
            </el-popover>
          </el-form-item>
        </el-form>
      </div>
    </div>
    <div class="panel">
      <el-switch v-model="autoLoad" active-text="自动刷新"></el-switch>
      <el-input
        type="textarea"
        ref="textarea"
        placeholder="请先选择日志类型与日志文件"
        :readonly="true"
        :autosize="{ minRows: 20, maxRows: 30}"
        v-model="content"
      ></el-input>
    </div>
  </div>
</template>
<script>
var title = "程序日志",
  that,
  timer;
Spa.define(
  {
    mixins: [mixinLists],
    data: function() {
      return {
        title: title,
        SpaTitle: title + " - %s",
        lists: [],
        types: [],
        currentType: null,
        current: null,
        content: "",
        stateTip: false,
        ban: true,
        autoLoad: true
      };
    },
    created: function() {
      that = this;
    },
    beforeDestroy: function() {
      clearTimeout(timer);
    },
    watch: {
      current: function(v) {
        if (v) {
          that.current = v;
          that.getDate();
        }
      },
      currentType: function(v, o) {
        if (v !== o) {
          that.current = "";
          that.content = "";
          that.lists = [];
          this.stateTip = false;
          that.getDate();
        }
        if (that.ban && v) {
          that.ban = false;
        }
      },
      autoLoad: function() {
        that.timeout();
      }
    },
    mounted: function() {
      that.getDate();
    },
    computed: {
      stateDel: function() {
        return !that.current;
      }
    },
    init: function(query, search) {},
    methods: {
      timeout: function() {
        clearTimeout(timer);
        if (that.autoLoad && that.current) {
          timer = setTimeout(function() {
            that.reloadDate();
          }, 1000);
        }
      },
      reloadDate: function() {
        that.getDate();
      },
      deleteLog: function() {
        that
          .$api(apis.systemLogsDelete, {
            name: that.current,
            type: that.currentType
          })
          .then(function() {
            that.current = "";
            that.reloadDate();
          })
          .finally(function() {
            setTimeout(function() {
              that.stateTip = false;
            });
          });
      },
      getDate: function() {
        that
          .$api(apis.systemLogs, { name: that.current, type: that.currentType })
          .then(function(e) {
            that.lists = e.data.lists;
            that.content = e.data.content;
            that.types = e.data.types;
            that.timeout();
          })
          .catch(function(err) {
            that.$warMsg(err);
          })
          .finally(function() {
            that.$nextTick(function() {
              var textarea = that.$refs.textarea.$el.querySelector("textarea");
              textarea.scrollTop = textarea.scrollHeight;
            });
          });
      }
    }
  },
  [],
  "/index"
);
</script>
