/**
 * Created by 影浅-seekwe@gmail.com on 2018-12-26
 */
var userTypesData = [{ title: '会员端', value: 'user' }, { title: '管理端', value: 'sys' }];
var navs = {
  'user': [
    { title: '后台中心', index: 'main', icon: 'icon-home' },
    { title: '链接管理', index: 'layout-user/link', icon: 'icon-attach' },
    { title: '查看统计', index: 'layout-user/count', icon: 'icon-bar-chart-' },
    // {
    //   title: '设置',
    //   index: 'setting',
    //   icon: 'icon-setting-fill',
    //   child: [
    //     { title: '用户管理', index: 'user/lists' },
    //     { title: '日志管理', index: 'user/logs' },
    //   ],
    // },
  ],
  'sys': [
    { title: '后台中心', index: 'main', icon: 'icon-home' },
    // { title: '审核管理', index: 'layout-sys/checklist', icon: 'icon-bulb-fill' },
    // { title: '广告管理', index: 'layout-sys/adlist', icon: 'icon-bulb-fill' },
    // { title: '广告状态管理', index: 'layout-sys/adstatuslist', icon: 'icon-bulb-fill' },
    // { title: '排行榜统计', index: 'layout-sys/checklist', icon: 'icon-bulb-fill' },
    { title: '返回链接', index: 'layout-sys/backlists', icon: 'icon-attach' },
    { title: '流量统计', index: 'layout-sys/flowlist', icon: 'icon-bar-chart' },
    { title: '域名管理', index: 'layout-sys/domains', icon: 'icon-globe' },
    { title: '域名白名单', index: 'layout-sys/whitelist', icon: 'icon-lock' },
    {
      title: '日志查看', icon: 'icon-alert-circle', index: '',
      child: [
        { title: '域名检测', index: 'layout-sys/log/domains', icon: 'icon-book' },
        { title: '程序错误', index: 'layout-sys/log/syserror', icon: 'icon-book' },
      ],
    },
  ],
};
