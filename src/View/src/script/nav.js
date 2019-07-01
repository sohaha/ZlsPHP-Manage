/**
 * Created by 影浅-seekwe@gmail.com on 2019-3-3
 */
var navs = {
  global: [
    {
      title: "后台中心",
      index: "main",
      breadcrumb: true, // 面包屑显示
      real: true, // 面包屑可点击
      show: true, // 导航栏显示
      icon: "icon-home",
      child: [
        {
          title: "站内消息",
          index: "user/logs",
          show: false,
          icon: "icon-settings-"
        },
        {
          title: "多端登录",
          index: "user/client",
          show: false,
          icon: "icon-globe--outline"
        }
      ]
    },
    {
      title: "站点管理",
      icon: "icon-pantone",
      index: "site/lists",
      real: true,
      child: [
        {
          title: "编辑站点",
          index: "site/info",
          show: false,
          icon: "icon-settings-"
        },

      ]
    },
    {
      title: "日志查看",
      icon: "icon-alert-circle",
      index: "system/logs"
    },
    {
      title: "系统设置",
      index: "system",
      breadcrumb: false,
      icon: "icon-options-",
      child: [
        {
          title: "程序设置",
          index: "system/config",
          icon: "icon-settings"
        },
        {
          title: "用户设置",
          index: "user/lists",
          icon: "icon-person"
        },
        {
          title: "角色设置",
          index: "user/group",
          icon: "icon-people"
        },
        {
          title: "菜单设置",
          index: "user/menu",
          icon: "icon-pricetags"
        },
        {
          title: "个人设置",
          index: "user/my",
          icon: "icon-person-done"
        },
        {
          title: "权限设置",
          index: "user/rules",
          icon: "icon-pantone"
        }
      ]
    }
  ],
  customize: {}
};
