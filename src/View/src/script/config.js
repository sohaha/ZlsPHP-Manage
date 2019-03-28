/**
 * Created by 影浅-seekwe@gmail.com on 2018-11-19
 */
var apiUrl = '';
var title = '管理后台';
var apis = {
  login: ['post', '/ZlsManage/UserApi/GetToken.go'],
  logout: ['post', '/ZlsManage/UserApi/ClearToken.go'], 
  systemLogs: ['get', '/ZlsManage/UserApi/SystemLogs.go'],
  systemLogsDelete: ['delete', '/ZlsManage/UserApi/SystemLogs.go'],
  sysUseriInfo: ['get', '/ZlsManage/UserApi/UseriInfo.go'],
  sysEditPassword: ['put', '/ZlsManage/UserApi/EditPassword.go'],
  sysUserLists: ['get', '/ZlsManage/UserApi/UserLists.go'],
  sysUploadAvatar: '/ZlsManage/UserApi/UploadAvatar.go',
  sysUpdateUser: ['put', '/ZlsManage/UserApi/Update.go'],
  sysCreateUser: ['post', '/ZlsManage/UserApi/Create.go'],
  sysDeleteUser: ['delete', '/ZlsManage/UserApi/Delete.go'],
  sysUserLogs: ['get', '/ZlsManage/UserApi/Logs.go'],
  sysUnreadMessageCount: ['get', '/ZlsManage/UserApi/UnreadMessageCount.go'],
  sysUpdateMessageStatus: ['put', '/ZlsManage/UserApi/MessageStatus.go'],
};