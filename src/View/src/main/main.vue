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
</style>
<template>
  <div class="main">
    <div class="view-title float-clear">
      <span>后台中心</span>
    </div>
    <div v-if="showError" class="panel">
      <el-alert v-for="(v,k) in error" :key="k" :title="v.tip" type="warning" :closable="false"></el-alert>
    </div>
    <div class="panel welcome">
      <i class="icon-bulb"></i> <span class="welcome-tip">{{nickname}}，欢迎回来！</span>
      <div class="welcome-logs">
        <el-button size="mini" v-if="isSys" @click="DetectionSystems">检测系统配置</el-button>
      </div>
    </div>
  </div>
</template>
<script>
  var that;
  Spa.define(
    {
      data: function () {
        return {
          SpaTitle: '后台中心 - %s', error: []
        };
      },
      created: function () {
        that = this;
      },
      computed: {
        isSys: function () {
          return that.$store.state.user.type === 'sys';
        },
        showError: function () {
          return this.error.length > 0;
        },
        nickname: function () {
          return that.$store.getters.nickname;
        }
      },
      mounted: function () {
      },
      methods: {
        gologs: function () {
          that.$go('user/logs');
        }
      }
    },
    [],
    '/index'
  );
</script>
