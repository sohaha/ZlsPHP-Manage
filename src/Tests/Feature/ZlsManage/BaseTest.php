<?php

namespace Feature\ZlsManage;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Z;
use Zls\Unit\Utils;

class BaseTest extends BaseTestCase
{
    use Utils;

    protected $username = 'admin';
    protected $password = 'admin';
    protected $token = '';
    protected $user = [];

    public function setUp(): void
    {
        // $this->command('migration r -t 0');
        // $this->command('migration m ');
        $this->command('manage passwd -u ' . $this->username . ' -p ' . $this->password);
    }

    public function testLogin()
    {
        $this->postJSON('/ZlsManage/UserApi/GetToken.go', [
            'user' => $this->username,
            'pass' => $this->password,
        ]);
        $arr  = $this->jsonToArr();
        $code = Z::arrayGet($arr, 'code');
        $this->assertEquals(200, $code);
        if ($code !== 200) {
            $this->fail(Z::arrayGet($arr, 'msg', '登录失败'));
        }
        $this->user  = Z::arrayGet($arr, 'data');
        $this->token = Z::arrayGet($arr, 'data.token');

        return Z::arrayGet($arr, 'data');
    }

    public function header($headers = []): array
    {
        return $headers + ['token:' . $this->token];
    }
}
