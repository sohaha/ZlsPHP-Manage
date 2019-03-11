<?php

namespace Bean\ZlsManage;

use Z;

class UserBean extends \Zls_Bean
{
    //id
    protected $id;

    //用户名
    protected $username;

    //用户密码
    protected $password;

    //密码盐
    protected $key;

    //用户昵称
    protected $nickname;

    //头像
    protected $avatar;

    //状态:0待激活,1正常,2禁止
    protected $status;

    //创建时间
    protected $create_time;

    //更新时间
    protected $update_time;


    public function getId()
    {
        /** @var \Zls\Action\Id $ActionId */
        $ActionId = z::extension('Action\Id');

        return $this->id ? $ActionId->encode($this->id) : '';
    }
}
