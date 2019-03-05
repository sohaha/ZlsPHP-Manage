/**
 * Created by 影浅-seekwe@gmail.com on 2018-11-19
 */
var apis = {
  login: ['post', '/ZlsManage/UserApi/GetToken.go'],
  logout: ['post', '/ZlsManage/UserApi/ClearToken.go'],
  sysEditPassword: ['put', '/ZlsManage/UserApi/EditPassword.go'],
  sysUserLists: ['get', '/ZlsManage/UserApi/UserLists.go'],
  sysUploadAvatar: '/ZlsManage/UserApi/UploadAvatar.go',
  sysUpdateUser: ['put', '/ZlsManage/UserApi/Update.go'],
  sysCreateUser: ['post', '/ZlsManage/UserApi/Create.go'],
  sysDeleteUser: ['delete', '/ZlsManage/UserApi/Delete.go'],
  sysUserLogs: ['get', '/ZlsManage/UserApi/Logs.go'],
  sysUnreadMessageCount: ['get', '/ZlsManage/UserApi/UnreadMessageCount.go'],
  sysUpdateMessageStatus: ['put', '/ZlsManage/UserApi/MessageStatus.go'],


  userLinks: ['get', '/H5UserApi/Lists.go'],
  userLinksEdit: ['post', '/H5UserApi/ListsEdit.go'],
  ListsDelete: ['post', '/H5UserApi/ListsDelete.go'],
  ListsAddLists: ['post', '/H5UserApi/AddLists.go'],
  reloadUrlContent: ['post', '/H5UserApi/reloadUrlContent.go'],
  userCounts: ['get', '/H5UserApi/counts.go'],
  allUserCounts: ['get', '/H5SysApi/counts.go'],
  getUrlWhiteLists: ['get', '/H5SysApi/getUrlWhiteLists.go'],
  EditUrlWhiteLists: ['post', '/H5SysApi/EditUrlWhiteLists.go'],
  DeleteUrlWhite: ['post', '/H5SysApi/DeleteUrlWhite.go'],
  SysBackUrlLists: ['get', '/H5SysApi/BackUrlLists.go'],
  SysBackUrlSwitch: ['post', '/H5SysApi/BackUrlSwitch.go'],
  SysBackUrlcjPlanLists: ['get', '/H5SysApi/BackUrlcjPlanLists.go'],
  SysBackUrlcjPlanUpdate: ['post', '/H5SysApi/BackUrlcjPlanUpdate.go'],
  SysDeleteBackUrlcjPlan: ['post', '/H5SysApi/DeleteBackUrlcjPlan.go'],
  SysGetGlobalBackUrl: ['get', '/H5SysApi/GlobalBackUrl.go'],
  SysEditGlobalBackUrl: ['post', '/H5SysApi/GlobalBackUrl.go'],
  SysGetDomains: ['get', '/H5SysApi/GetDomainLists.go'],
  SysPutEditDomain: ['put', '/H5SysApi/Domain.go'],
  SysEditDomain: ['post', '/H5SysApi/Domain.go'],
  SysDeleteDomain: ['delete', '/H5SysApi/Domain.go'],
  SysRunDomainTask: ['post', '/H5SysApi/RunDomainTask.go'],
  SysDetectionSystems: ['get', '/H5SysApi/DetectionSystems.go'],
  SysBackUrlcjPlan: ['put', '/H5SysApi/BackUrlcjPlan.go'],
  SysLogDomains: ['get', '/H5LogApi/domains.go'],
  SysLogSyserror: ['get', '/H5LogApi/SysError.go'],
};
var api = function (name, data) {
  var api = (typeof name === 'string') ? (apis[name]||"") : name;

  return !!api
    ? (typeof api === 'string' ? api : (function () {
      var url = api[1];
      if (typeof data === 'string') {
        url += (url.indexOf('?') > -1 ? '&' : '?') + data;
        data = {};
      }
      return this[api[0]] ? this[api[0]](url, data) : this['request'](api[0], url, data);
    })())
    : (function () {
      var p = Spa.promise();
      setTimeout(function () {
        typeof api === 'undefined' ? p.resolve() : p.reject('未知接口: ' + name);
      });
      return p;
    }());
};
