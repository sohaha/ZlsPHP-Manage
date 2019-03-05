<?php

namespace Dao\ZlsManage;

use Z;

class LogsDao extends \Zls_Dao
{
    const TYPE_NORMAL = 1;
    const TYPE_WARN = 2;
    const TYPE_ERROR = 3;
    const STATUS_NOT = 1;
    const STATUS_READ = 2;

    public function getColumns()
    {
        return [
            'id'//id
            ,
            'userid'//对应用户Id
            ,
            'operate_id'//操作人Id，游客为0
            ,
            'content'//信息
            ,
            'create_time'//创建时间
            ,
            'update_time'//更新时间
            ,
            'type'//状态:1正常，2警告，3错误
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
        return 'auth_user_logs';
    }


    public function create($userid, $title, $content, $operateid = 0, $type = self::TYPE_NORMAL, $status = self::STATUS_NOT)
    {
        $data = [
            'userid'      => $userid,
            'operate_id'  => $operateid,
            'type'        => $type,
            'status'      => $status,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'content'     => $content,
            'title'       => $title,
        ];

        return $this->insert($data);
    }

    public function createOperationLog($userid, $title, $content, $operateid = 0, $type = self::TYPE_NORMAL, $status = self::STATUS_NOT)
    {
        $ip = Z::clientIp();
        $ua = Z::server('HTTP_USER_AGENT');
        if (!$title) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            $title = mb_substr($content, 0, 50);
        }
        $content = "{$content}\nOperate IP: {$ip}\nUser Agent: {$ua}";

        return $this->create($userid, $title, $content, $operateid, $type, $status);
    }
}
