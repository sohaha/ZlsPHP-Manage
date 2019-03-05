<?php

use Zls\Migration\AbstractMigration as M;
use Zls\Migration\MysqlAdapter;

/**
 * 管理员表
 * Class Users
 */
class Users extends M
{
    public function change()
    {
        $table = $this->table('auth_user');
        $table->comment('管理员');
        $table->addColumn('username', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '用户名']);
        $table->addColumn('password', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '用户密码']);
        $table->addColumn('key', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '密码盐']);
        $table->addColumn('nickname', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '用户昵称']);
        $table->addColumn('email', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => 'Email']);
        $table->addColumn('remark', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '用户简介']);
        $table->addColumn('avatar', self::TYPE_STRING, [self::OPTIONS_DEFAULT => '', self::OPTIONS_COMMENT => '头像']);
        $table->addColumn('status', self::TYPE_INTEGER, [self::OPTIONS_DEFAULT => 0, 'limit' => MysqlAdapter::INT_TINY, self::OPTIONS_COMMENT => '状态:-1软删除,0待激活,1正常,2禁止']);
        $table->addColumn('group_id', self::TYPE_INTEGER, [self::OPTIONS_DEFAULT => 0, 'limit' => MysqlAdapter::INT_MEDIUM, self::OPTIONS_COMMENT => '角色Id']);
        $table->addColumn('create_time', self::TYPE_TIMESTAMP, [self::OPTIONS_DEFAULT => null, self::OPTIONS_NULL => true, self::OPTIONS_COMMENT => '创建时间']);
        $table->addColumn('update_time', self::TYPE_TIMESTAMP, [self::OPTIONS_DEFAULT => null, self::OPTIONS_NULL => true, self::OPTIONS_COMMENT => '更新时间']);
        $table->addIndex(['username', 'status'], ['name' => 'username_status']);
        $table->create();
        /** @var \Zls\Action\StrUtils $StrUtils */
        $StrUtils    = z::extension('Action\StrUtils');
        $key         = $StrUtils->randString();
        $defaultPass = 'seekwe';// 默认密码
        $singleRow   = [
            'username'    => 'seekwe',
            'key'         => $key,
            'status'      => 1,
            'email'       => 'seekwe@gmail.com',
            'password'    => md5(Z::encrypt($defaultPass, '', $key)),
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $table->insert($singleRow);
        $table->saveData();
    }
}
