<style>
  .header {
    background-color: #fff;
    color: #333;
    line-height: 60px;
    padding: 0 40px;
    z-index: 9;
    box-shadow: 0 1px 4px 0 #c0c4cc;
    /*box-shadow: 0 1px 4px 0 rgba(238, 238, 238, 0.5);*/
  }

  .header-logo {
    padding: 0;
    font-size: 30px;
  }

  .header-logo img {
    vertical-align: middle;
    height: 45px;
    margin-bottom: 10px;
  }

  .header-nav {
    padding: 0;
    height: 60px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    overflow: hidden;
    -ms-flex-direction: row-reverse;
    -webkit-box-orient: horizontal;
    -webkit-box-direction: reverse;
    flex-direction: row-reverse;
  }

  .header-nav > div {
    width: 80px;
    padding-top: 3px;
    text-align: center;
  }

  .header-nav > div:hover {
    background-color: #eaf1f7;
  }

  .tip-msg {
    font-size: 30px;
    line-height: 20px;
    padding-bottom: 10px;
  }

  .header-avatar {
    width: 45px;
    height: 45px;
    overflow: hidden;
    vertical-align: middle;
    border-radius: 50%;
    box-shadow: 1px 0 3px;
    margin: 5px 20px 0 5px;
  }

  .el-dropdown-menu.el-popper {
    white-space: nowrap;
    margin-top: 5px !important;
  }

  .header-nav .user-menu.el-dropdown {
    height: 60px;
    line-height: 25px;
    display: block;
    width: 75px;
    text-align: center;
    float: right;
    box-sizing: border-box;
    overflow: hidden;
  }

  .header-nav .el-icon--right {
    position: absolute;
    right: 5px;
    top: 25px;
  }

  .header-name {
    display: block;
    font-size: 12px;
    color: #999;
    border-top: 1px solid #e4e8eb;
    margin-top: 5px;
    padding-top: 2px;
    line-height: 12px;
  }
</style>
<template>
  <el-container>
    <el-aside class="header-logo tap" @click.native.prevent="handleNav">
      <img src="./src/images/logo.png" alt="Logo" />
    </el-aside>
    <el-main class="header-nav">
      <div class="tap">
        <el-dropdown trigger='click' class="user-menu" @command="clickMenu">
          <div>
            <img class="header-avatar" :src="avatar" alt='' /> <i class="el-icon-caret-bottom el-icon--right"></i>
            <!-- <span class="header-name text-nowrap">{{ nickname }}</span> -->
          </div>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item command="user">
              <i class="icon-person-outline"></i> <span>账号信息</span>
            </el-dropdown-item>
            <el-dropdown-item command="logs">
              <i class="icon-award-outline"></i> <span>查看消息</span>
            </el-dropdown-item>
            <el-dropdown-item command="editPassword" divided>
              <i class="icon-unlock-outline"></i> <span>修改密码</span>
            </el-dropdown-item>
            <el-dropdown-item command="logout">
              <i class="icon-log-out"></i> <span>退出登录</span>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
      <div @click="gologs" class="tap" v-show="unreadMessageCount">
        <el-badge :value="unreadMessageCount" class="tip-msg">
          <i class="icon-email-outline"></i>
        </el-badge>
      </div>
    </el-main>
  </el-container>
</template>
<script>
  var that, MessageCountTime;
  Spa.define({
    data: function () {
      return {
        passViewDialogVisible: false,
      };
    },
    created: function () {
      that = this;
    },
    props: {},
    computed: {
      nickname: function () {
        return this.$store.getters.nickname;
      },
      avatar: function () {
        return this.$store.getters.avatar;
      },
      unreadMessageCount: function () {
        return this.$store.state.unreadMessageCount;
      },
    },
    mounted: function () {
      this.getUnreadMessageCount();
      window['SysGetUnreadMessageCount'] = this.getUnreadMessageCount;
    },
    beforeDestroy: function () {
      setTimeout(function () {
        clearTimeout(MessageCountTime);
      });
    },
    methods: {
      gologs: function () {
        that.logs();
      },
      getUnreadMessageCount: function () {
        clearTimeout(MessageCountTime);
        that.$api(apis.sysUnreadMessageCount)
            .then(function (e) {
              that.$store.commit('setUnreadMessageCount', e.data.count);
            })
            .finally(function () {
              if (that.$store.getters.token) MessageCountTime = setTimeout(that.getUnreadMessageCount, 5000);
            });
      },
      handleNav: function () {
        that.$emit('handle');
      },
      clickMenu: function (command) {
        if (that[command]) {
          that[command]();
        }
        that.$emit('click', command);
      },
      user: function () {
        that.$go('user/@' + that.$store.state.user.username + '/lists');
      },
      logs: function () {
        that.$go('user/logs?v=' + (+new Date()));
      },
      logout: function () {
        that.$api(apis.logout)
            .then(function (e) {
              that.$store.commit('setToken', '');
              that.$store.commit('setUser', {});
              Spa.go('/login');
            });
      },
    },
  });
</script>
