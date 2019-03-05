<?php

namespace Controller;

use Z;
use Dao\ZlsManage\UserDao;
use Business\ZlsManage\UserBusiness;

/**
 * Class ZlsManage
 * @package Controller
 */
class ZlsManage extends \Zls_Controller
{
    public function zIndex()
    {
        Z::redirect('/ZlsManage/index.html');
    }

    protected $USER;
    /** @var \Business\ZlsManage\UserBusiness $UserBusiness */
    protected $UserBusiness;

    public function before($method, $controllerShort, $args, $controller)
    {
        $methodName         = z::strCamel2Snake(str_replace('\\', '_', $controllerShort) . '_' . $method, '');
        $noVerification     = ['zlsmanage_userapi_gettoken', 'user_index'];
        $this->UserBusiness = new UserBusiness;
        if (!in_array($methodName, $noVerification)) {
            $token      = $this->getToken();
            $this->USER = $this->UserBusiness->tokenToInfo($token);
            if (!$this->USER) {
                return z::json(401, '请先登录');
            }
        }

        return null;
    }

    final protected function getToken()
    {
        return z::server('HTTP_TOKEN') ?: z::getPost('token');
    }

    final public function getInfo($key = null)
    {
        return $key ? z::arrayGet($this->USER, $key) : $this->USER;
    }

    public function after($contents, $methodName, $controllerShort, $args, $controller)
    {
        return $contents;
    }

    public function call($method, $controllerShort, $args, $controller)
    {
        return z::json(404);
    }
}
