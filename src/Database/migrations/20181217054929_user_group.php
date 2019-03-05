<?php

use Zls\Migration\AbstractMigration as M;

class UserGroup extends M
{
    public function change()
    {
        $table = $this->table('auth_user_group');
        $table->comment('用户角色');
        $table->addColumn('create_time', 'timestamp', ['default' => null, 'null' => true, 'comment' => '创建时间']);
        $table->addColumn('update_time', 'timestamp', ['default' => null, 'null' => true, 'comment' => '更新时间']);
        $table->addColumn('type', 'integer', ['default' => 1, 'limit' => 255, 'comment' => '状态:1正常，2禁止']);
        $table->addColumn('rule_ids', 'string', ['default' => '', 'comment' => '规则id，多个;分隔']);
        $table->create();
    }
}
