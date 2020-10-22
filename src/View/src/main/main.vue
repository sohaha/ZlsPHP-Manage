<style>
.welcome {
  line-height: 20px;
  font-size: 15px;
}

.welcome i {
  font-size: 20px;
  vertical-align: middle;
}

.welcome-tip {
  vertical-align: middle;
}

.welcome-logs {
  font-size: 13px;
  float: right;
  color: #9a9a9a;
}

.main .el-alert--warning {
  margin-bottom: 10px;
}

.show-percent {
  content: " %";
}
</style>
<template>
  <div class="main">
    <div class="view-title float-clear"></div>
    <div v-if="showError" class="panel">
      <el-alert
        v-for="(v, k) in error"
        :key="k"
        :title="v.tip"
        type="warning"
        :closable="false"
      ></el-alert>
    </div>
    <fieldset>
      <aside>
        <div class="tip-area" v-if="last[0]">
          最近登录：{{ last[0] }} 【{{ last[1] }}】
        </div>
        <i class="icon-bulb"></i>
        <span class="welcome-tip">{{ hi }}，欢迎回来 ！</span>
      </aside>
    </fieldset>
    <el-row v-if="system" :gutter="20">
      <el-col :md="16" :span="24">
        <fieldset>
          <legend>系统信息</legend>
          <aside>
            <li>
              <span>核心版本</span>
              <span>{{ system.zls_version || " -- " }}</span>
            </li>

            <li>
              <span>主机系统</span> <span>{{ system.os || " -- " }}</span>
            </li>

            <li>
              <span>PHP版本</span>
              <span>{{ system.php_version || " -- " }}</span>
            </li>

            <li>
              <span>PHP环境</span> <span>{{ system.software || " -- " }}</span>
            </li>

            <li>
              <span>服务器名</span> <span>{{ system.hostname || " -- " }}</span>
            </li>

            <li>
              <span style="padding: initial">
                <span>物理内存</span>
                <span :style="system.memory.total ? 'color: #3f51b5;' : ''">
                  {{ system.memory.total || " -- " }}
                </span>
              </span>
              <span style="padding: initial">
                <span>已用</span>
                <span :style="system.memory.used ? 'color: #CC0000;' : ''">
                  {{ system.memory.used || " -- " }}
                </span>
                <span>空闲</span>
                <span :style="system.memory.free ? 'color: #CC0000;' : ''">
                  {{ system.memory.free || " -- " }}
                </span>
                <span>使用率</span>
                <span :style="memoryUsageStyle(system.memory.usage)" :class="{ 'show-percent': system.memory.usage }">
                  {{ system.memory.usage || " -- " }}
                </span>
              </span>
            </li>

            <li>
              <span style="padding: initial">
                <span>磁盘空间</span>
                <span :style="system.disk.total ? 'color: #3f51b5;' : ''">
                  {{ system.disk.total || " -- " }}
                </span>
              </span>
              <span style="padding: initial">
                <span>剩余</span>
                <span :style="diskFreeStyle(system.disk.free)">
                  {{ system.disk.free || " -- " }}
                </span>
              </span>
            </li>

            <li>
              <span>支持扩展</span>
              <span class="text">{{ system.extensions || " -- " }}</span>
            </li>

            <li>
              <span>禁用函数</span>
              <span class="text">{{ system.disable_functions || " -- " }}</span>
            </li>
          </aside>
        </fieldset>
      </el-col>
      <el-col :md="8" :span="24">
        <fieldset>
          <legend>扩展信息</legend>
          <aside>
            <li v-for="(o, k) in composer" :key="k">
              <span>{{ o.name }}</span> <span>{{ o.version }}</span>
            </li>
          </aside>
        </fieldset>
      </el-col>
    </el-row>
  </div>
</template>
<script>
var that;
Spa.define(
  {
    mixins: [initTitle],
    data: function () {
      return {
        title: "后台中心",
        error: [],
      };
    },
    beforeCreate: function () {
      that = this;
    },
    computed: {
      hi: function () {
        var now = new Date(),
          hour = now.getHours(),
          text = "";
        switch (true) {
          case hour < 6:
            text = "凌晨好";
            break;
          case hour < 9:
            text = "早上好";
            break;
          case hour < 12:
            text = "上午好";
            break;
          case hour < 14:
            text = "中午好";
            break;
          case hour < 17:
            text = "下午好";
            break;
          case hour < 19:
            text = "傍晚好";
            break;
          case hour < 22:
            text = "晚上好";
            break;
          default:
            text = "深夜好";
        }
        return text + "，" + this.nickname;
      },
      last: function () {
        var last = Object.assign({}, that.$store.state.user.last || {});
        return [last.create_time, last.ip];
      },
      systemInfo: function () {
        var system = Object.assign({}, that.$store.state.user.system || {});
        var composer = system["composer"];
        delete system["composer"];
        return [system, composer];
      },
      system: function () {
        var system = that.systemInfo[0];
        return JSON.stringify(system) === "{}" ? null : that.systemInfo[0];
      },
      composer: function () {
        return that.systemInfo[1];
      },
      isSys: function () {
        return that.$store.state.user.type === "sys";
      },
      showError: function () {
        return this.error.length > 0;
      },
      nickname: function () {
        return that.$store.getters.nickname;
      },
    },
    mounted: function () {
      console.log("main");
    },
    methods: {
      DetectionSystems: function () {},
      gologs: function () {
        that.$go("user/logs");
      },
      memoryUsageStyle: function (val) {
        if (!val) {
          return "";
        }
        return {
          color: val >= 70 ? "red" : "green",
        };
      },
      diskFreeStyle: function (val) {
        var v = parseInt(val, 10);
        if (!v) {
          return "";
        }
        return {
          color: v <= 10 ? "red" : "#CC0000",
        };
      },
    },
  },
  [],
  "/index"
);
</script>
