<?php

namespace Business\ZlsManage;

use Dao\ZlsManage\LogsDao;
use Dao\ZlsManage\TokenDao;
use Dao\ZlsManage\UserDao;
use Z;

/**
 * Class UserBusiness
 * @package Business\User
 */
class UserBusiness extends \Zls_Business
{
    /**
     * 根据token获取用户信息
     * @param $token
     * @return array
     */
    public function tokenToInfo($token)
    {
        /** @var \Dao\ZlsManage\TokenDao $tokenDao */
        $tokenDao = new TokenDao();
        $user     = [];
        if ($tokenInfo = $tokenDao->find(['token' => $token, 'status' => 1], false, [])) {
            /** @var \Dao\ZlsManage\UserDao $dao */
            $dao  = z::dao('ZlsManage\UserDao');
            $user = $dao->find($tokenInfo['userid']);
        }

        return $user;
    }

    /**
     * 过滤用户密码等敏感字段
     * @param $user
     * @return array[]
     */
    public function filterField($user)
    {
        /*** @var \Dao\ZlsManage\UserDao $Dao */
        $Dao = z::dao('ZlsManage\UserDao', true);

        return z::readData($Dao->getReversalColumns(), $user);
    }

    /**
     * 用户名获取用户信息
     * @param $username
     * @return array
     */
    public function nameToInfo($username)
    {
        /** @var \Dao\ZlsManage\UserDao $dao */
        $dao  = new UserDao();
        $user = $dao->find(['username' => $username], false, [], ['*']);
        if ($user) {
            $user = $dao->bean($user);
        }

        return $user;
    }

    /**
     * 创建token
     * @param string $idEncode 编码后的用户Id
     * @return string
     */
    public function createToken($idEncode)
    {
        /** @var \Zls\Action\Id $ActionId */
        $ActionId = z::extension('Action\Id');
        $id       = $ActionId->decode($idEncode);

        return z::tap(Z::encrypt($id . '_' . date('y-m-d H:i:s') . '_' . $ActionId->uniqueId(4)),
            function ($token) use ($id) {
                $tokenDao = new TokenDao();
                $tokenDao->saveToken($id, $token);
                $dao = new LogsDao();
                $dao->createOperationLog($id, null, '登录成功，欢迎回来！', $id, $dao::TYPE_NORMAL, $dao::STATUS_READ);
            });
    }

    /**
     * 清除用户Token
     * @param $token
     * @return int
     */
    public function clearToken($token)
    {
        return (new TokenDao())->update(['status' => 2], ['status' => 1, 'token' => $token]);
    }

    /**
     * 清除指定用户全部token
     * @param $adminid
     * @return bool
     */
    public function clearAllToken($adminid)
    {
        return (new TokenDao())->update(['status' => 2], ['status' => 1, 'userid' => $adminid]);
    }

    /**
     * 用户列表
     * @param        $page
     * @param int    $pagesize
     * @param string $key
     * @return array
     */
    public function lists($page, $pagesize = 10, $key = '')
    {
        $dao  = new UserDao();
        $data = [];
        if ($key) {
            $dao->getDb()->where(['username like' => "%{$key}%"]);
        }
        $fields = $dao->getReversalColumns(null, true);
        $dao->getDb()->select('count(*) as total')
            ->from($dao->getTable());
        $total = $dao->getDb()->execute()->value('total');
        if ($key) {
            $dao->getDb()->where(['username like' => "%{$key}%"]);
        }
        if ($page < 1) {
            $page = 1;
        }
        if ($pagesize < 1) {
            $pagesize = 1;
        }
        $dao->getDb()
            ->select($fields)
            ->limit(($page - 1) * $pagesize, $pagesize)
            ->from($dao->getTable())
            ->orderBy('id', 'desc');
        $result        = $dao->getDb()->execute()->rows();
        $data['items'] = $result;
        $data['page']  = Z::page($total, $page, $pagesize, '{page}');

        return $data;
    }

    /**
     * 更新用户
     * @param     $id
     * @param     $data
     * @param int $uid
     * @return int|string
     */
    public function update($id, $data, $uid = 0)
    {
        $dao = new UserDao();
        if ($id == $uid && 1 != z::arrayGet($data, 'status')) {
            return '不能禁止自己';
        }
        $rule    = $dao->verifyRules(['status', 'remark', 'avatar', 'email']);
        $retData = $errorMsg = $errorKey = null;
        if (z::checkData($data, $rule, $retData, $errorMsg, $errorKey, $dao->getDb())) {
            $map                    = ['status', 'avatar', 'remark', 'email'];
            $retData                = z::readData($map, $retData);
            $retData['update_time'] = date('Y-m-d H:i:s');
            if ($retData['avatar']) {
                /**
                 * @var \Business\ZlsManage\AssistBusiness $AssistBusiness
                 */
                $AssistBusiness    = z::business('ZlsManage\AssistBusiness');
                $retData['avatar'] = $AssistBusiness->mvAvatar($retData['avatar']);
            }

            return $dao->update($retData, $id);
        } else {
            return $errorMsg;
        }
    }

    /**
     * 建立用户
     * @param $data
     * @return string|int
     */
    public function create($data)
    {
        $dao     = new UserDao();
        $map     = $dao->getReversalColumns(['id']);
        $rule    = $dao->verifyRules(array_keys($data));
        $retData = $errorMsg = $errorKey = null;
        if (z::checkData($data, $rule, $retData, $errorMsg, $errorKey, $dao->getDb())) {
            $retData                = z::readData($map, $retData, false);
            $date                   = date('Y-m-d H:i:s');
            $retData['create_time'] = $retData['update_time'] = $date;
            /** @var \Zls\Action\StrUtils $StrUtils */
            $StrUtils            = z::extension('Action\StrUtils');
            $key                 = $StrUtils->randString();
            $retData['key']      = $key;
            $retData['password'] = $dao->encryptPassword($retData['password'], $key);

            return (int)$dao->insert($retData);
        } else {
            return $errorMsg;
        }
    }

    /**
     * 删除用户
     * @param $userid
     * @return int
     */
    public function delete($userid)
    {
        $UserDao = new UserDao();
        $count   = $UserDao->find(null, false, [], 'count(*) as count');
        if (z::arrayGet($count, 'count') <= 1) {
            return '不予许删除唯一用户';
        }

        return $UserDao->delete($userid);
    }

    /**
     * 获取真实用户id
     * @param $userid
     * @return int
     */
    public function decodeUserid($userid)
    {
        return z::extension('Action\Id')->decode($userid);
    }

    public function checkPass(array $user, $pass)
    {
        $UserDao = new UserDao();
        $res     = true;
        switch (true) {
            case !$user:
                $res = '用户不存在';
                break;
            case ($UserDao->encryptPassword($pass, $user['key']) !== $user['password']):
                $dao = new LogsDao();
                $dao->createOperationLog($this->decodeUserid($user['id']), null, "游客尝试登录【{$user['username']}】账号！", 0, LogsDao::TYPE_WARN);
                $res = '密码错误';
                break;
            case $UserDao::STATUS_WAIT == $user['status']:
                $res = '用户待激活';
                break;
            case $UserDao::STATUS_BAN == $user['status']:
                $res = '用户已停用';
                break;
            default:
        }

        return $res;
    }

    /**
     * 修改用户密码
     * @param $userid
     * @param $oldPassword
     * @param $newPassword
     * @return string|int
     */
    public function editPassword($userid, $oldPassword, $newPassword)
    {
        $UserDao = new UserDao();
        $where   = ['id' => $userid];
        $user    = $UserDao->find($where, false, [], '*');
        if (!$user) {
            return '用户不存在';
        } elseif (md5(z::encrypt($oldPassword, '', $user['key'])) !== $user['password']) {
            return '原密码错误';
        } else {
            /** @var \Zls\Action\StrUtils $StrUtils */
            $StrUtils          = z::extension('Action\StrUtils');
            $data['key']       = $StrUtils->randString();
            $data ['password'] = $UserDao->encryptPassword($newPassword, $data['key']);

            return $UserDao->update($data, $where);
        }
    }
}
