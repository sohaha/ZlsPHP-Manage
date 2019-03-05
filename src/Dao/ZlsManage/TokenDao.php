<?php

namespace Dao\ZlsManage;

use z;

class TokenDao extends \Zls_Dao
{
    const STATUS_OPEN = 1;
    const STATUS_BAN = 2;

    public function getColumns()
    {
        return [
            'id'//id
            ,
            'userid'//管理员Id
            ,
            'token'//token
            ,
            'ip'//登录IP
            ,
            'ua'//User Agent
            ,
            'create_time'//创建时间
            ,
            'update_time'//更新时间
            ,
            'status'//状态:1正常,2禁止
        ];
    }

    public function getHideColumns()
    {
        return [];
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getTable()
    {
        return 'auth_user_token';
    }

    public function saveToken($userid, $token)
    {
        $data = date('Y-m-d H:i:s');
        $data = [
            'userid'      => $userid,
            'token'       => $token,
            'ip'          => z::clientIp(),
            'ua'          => z::server('HTTP_USER_AGENT'),
            'create_time' => $data,
            'update_time' => $data,
            'status'      => self::STATUS_OPEN,
        ];

        return $this->insert($data);
    }

}
