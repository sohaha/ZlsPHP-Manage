<?php

namespace Feature\ZlsManage;

use Z;

class UserTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        if (!$this->user) {
            // depends
            $this->testLogin();
        }
    }

    public function testUseriInfo(): void
    {
        $this->getJSON('/ZlsManage/UserApi/UseriInfo.go', [], $this->header());
        $data = $this->jsonToArr();
        $this->assertEquals(Z::arrayGet($data, 'data.username'), Z::arrayGet($this->user, 'username'));
    }

    public function testUseriUpdate(): void
    {
        $this->putJSON('/ZlsManage/UserApi/Update.go', [
            'id'       => $this->user['id'],
            'status'   => 1,
            'nickname' => '666',
        ], $this->header());
        $data = $this->jsonToArr();
        $this->assertEquals(200, Z::arrayGet($data, 'code'));
    }

}
