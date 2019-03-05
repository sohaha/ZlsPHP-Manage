<?php

namespace Controller\ZlsManage;

use Business\ZlsManage\AssistBusiness;
use Controller\ZlsManage;
use Dao\ZlsManage\LogsDao;
use Dao\ZlsManage\UserDao;
use Z;

/**
 * 后台-用户接口
 * @author        影浅
 * @email         seekwe@gmail.com
 * @copyright     Copyright (c) 2015 - 2017, 影浅, Inc.
 * @link          ---
 * @since         v0.0.1
 * @updatetime    2017-05-30 21:32
 * @desc          需要发送用户Token（GetToken除外）
 */
class UserApi extends ZlsManage
{
    /**
     * 获取用户Token
     * @time 2018-11-1 13:46:28
     * @param string user 用户名 POST '' y
     * @param string pass 密码 POST '' y
     * @return object
     * @return string data.token 鉴权token
     */
    public function POSTzGetToken()
    {
        $user   = z::postGet('user');
        $pass   = z::postGet('pass');
        $user   = $this->UserBusiness->nameToInfo($user);
        $status = $this->UserBusiness->checkPass($user, $pass);
        if (is_string($status)) {
            return z::json(211, $status);
        } else {
            $user          = $this->UserBusiness->filterField($user);
            $user['token'] = $this->UserBusiness->createToken($user['id']);

            return z::json(200, '登录成功', $user);
        }
    }

    /**
     * 用户详情
     * @time 2018-11-8 17:57:48
     */
    public function GETzUseriInfo()
    {
        return z::json(200, '用户详情', $this->getInfo());
    }


    /**
     * 修改用户密码
     * @param string userid 要修改的用户id PUT 当前用户id n
     * @param string oldPass 当前密码 PUT '' y
     * @param string pass 新密码 PUT '' y
     * @param string pass 确认密码 PUT '' y
     * @time 2018-12-12 17:06:25
     * @return object
     */
    public function PUTzEditPassword()
    {
        $oldPassword = z::postText('oldPass');
        $newPassword = z::postText('pass');
        $userid      = $this->getInfo('id');
        $upUid       = z::postText('userid') ?: $userid;
        if ($userid == $upUid) {
            $rs = $this->UserBusiness->editPassword($upUid, $oldPassword, $newPassword);
        } else {
            $rs = '不能修改其他人密码';
        }

        return is_string($rs) ? z::json(211, $rs) : z::json(200, '修改密码成功', $rs);
    }

    /**
     * 查看用户日志
     * @time 2018-12-13 19:15:42
     * @param int page 页码 GET 1 n
     * @param int pagesize 条数 GET 10
     * @param int unread 是否只查询未读 GET 0 N 1只查询未读
     * @return mixed|string
     */
    public function GETzLogs()
    {
        $assist = new AssistBusiness();
        $type   = Z::get('type');
        $unread = z::get('unread', 0);

        return z::json(200, '用户日志', $assist->getLogs(Z::get('page', 1), z::get('pagesize', 10), $unread, $type));
    }

    /**
     * 未读日志总数
     */
    public function GETzUnreadMessageCount()
    {
        $assist = new AssistBusiness();
        $lastId = z::get('id', 0);
        $userid = $this->getInfo('id');

        // 可以在这里做token时间更新
        return z::json(200, '未读日志', ['count' => $assist->getUnreadMessageCount($lastId, $userid)]);
    }

    /**
     * 更新日志状态
     */
    public function PUTzMessageStatus()
    {
        $assist = new AssistBusiness();
        $uid    = $this->getInfo('id');
        $ids    = z::postText('ids');

        return z::json(200, '日志标记已读', $assist->updateMessageStatus($ids, $uid));
    }

    /**
     * 获取用户列表
     * @time 2018-11-7 17:57:39
     * @param int page 页码 GET 1 n
     * @param int pagesize 条数 GET 10
     * @param string key 用户名/Email GET '' n 为空表示全部用户
     * @return object
     */
    public function GETzUserLists()
    {
        $page     = z::get('page', 1);
        $pagesize = z::get('pagesize', 10);
        $key      = z::get('key');

        return z::json(200, '用户列表', $this->UserBusiness->lists($page, $pagesize, $key));
    }

    /**
     * 更新用户资料
     * @param int id 用户ID PUT '' y 注意：如果空表示更新自己
     * @time 2019-3-4 19:30:56
     * @return object
     */
    public function PUTzUpdate()
    {
        $uid    = $this->getInfo('id');
        $userid = z::postText('id') ?: $uid;
        $post   = z::postText();
        $rs     = $this->UserBusiness->update($userid, $post, $this->getInfo('id'));

        return is_string($rs) ? z::json(211, $rs) : z::json(200, '处理成功', ['id' => $rs]);
    }

    /**
     * 创建新用户
     * @time 2019-3-4 19:30:56
     */
    public function POSTzCreate()
    {
        $post = z::postText();
        $rs   = $this->UserBusiness->create($post);

        return is_string($rs) ? z::json(211, $rs) : z::json(200, '处理成功', ['id' => $rs]);
    }

    /**
     * 删除用户
     * @desc 该操作只是软删除用户
     * @time 2018-11-9 16:56:55
     * @param int id 用户id POST '' y
     * @return object
     */
    public function DELETEzDelete()
    {
        $id = (int)z::postText('id');
        if ($id === z::arrayGet($this->USER, 'id')) {
            $result = '不可以删除自己';
        } else {
            $result = $this->UserBusiness->delete($id);
        }

        return is_string($result)
            ? z::json(211, $result)
            : z::tap(z::json(200, '删除用户', $result), function () use ($id) {
                $this->UserBusiness->clearAllToken($id);
            });
    }

    /**
     * 上传用户头像
     * @time      2018-11-8 18:56:59
     * @api-param raw file 文件域 POST null y 文件大小不能超过1024kb
     */
    public function POSTzUploadAvatar()
    {
        $AssistBusiness = new AssistBusiness();
        $result         = $AssistBusiness->uploadAvatar();
        if (is_string($result)) {
            return z::json(211, $result);
        }

        return z::json(200, '上传成功', ['path' => $result['url'], 'host' => z::host()]);
    }

    /**
     * 清除用户Token
     * @time 2018-11-7 17:57:31
     */
    public function POSTzClearToken()
    {
        $token  = $this->getToken();
        $result = $this->UserBusiness->clearToken($token);

        return z::json(200, '清除用户Token', $result);
    }
}
