<?php

namespace Zls\Manage\Command;

use Dao\ZlsManage\UserDao;
use Z;
use Zls\Action\StrUtils;

class Manage extends \Zls\Command\Command
{
    use Replace;
    private $force;

    /**
     * 命令配置.
     * @return array
     */
    public function options()
    {
        // TODO: Implement options() method.
    }

    /**
     * 命令介绍.
     * @return string
     */
    public function description()
    {
        return 'General management';
    }

    /**
     * 命令说明
     * @return array
     */
    public function commands()
    {
        return [
            ' init'     => [
                'Initialize resources',
                [
                    '--force, -F' => ' Overwrite old file',
                ],
            ],
            ' initView' => [
                'Initialize view resources',
                [
                    '--force, -F' => ' Overwrite old file',
                ],
            ],
            ' passwd'   => [
                '更新指定账户密码',
                [
                    '-u' => ' username',
                    '-p' => ' password',
                ],
            ],
        ];
    }

    /**
     * 命令默认执行.
     *
     * @param $args
     */
    public function execute($args)
    {
        $active = z::arrayGet($args, 2);
        if (method_exists($this, $active)) {
            $this->$active($args);
        } else {
            $this->help($args);
        }
    }

    private function initView()
    {
        $dest   = Z::realPathMkdir('./zls-manage', true, false, true);
        $source = Z::realPath(__DIR__ . '/../View', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('View success');
    }

    private function initBusiness()
    {
        $dest   = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBusinessDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Business', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Business success');
    }

    private function initController()
    {
        $dest   = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getControllerDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Controller', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Controller success');
    }

    private function initDao()
    {
        $dest   = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getDaoDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Dao', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Dao success');
    }

    private function initBean()
    {
        $dest   = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBeanDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Bean', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Bean success');
    }

    private function initTest()
    {
        $dest   = Z::realPath('./tests', true, false);
        $source = Z::realPath(__DIR__ . '/../Tests', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Tests success');
    }

    private function initDatabase()
    {
        $dest   = Z::realPathMkdir('./database', true, false, false);
        $source = Z::realPath(__DIR__ . '/../Database', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Database success');
    }

    public function init($args)
    {
        $this->force = Z::arrayGet($args, ['-force', 'F']);
        $this->initDatabase();
        $this->initView();
        $this->initBusiness();
        $this->initController();
        $this->initDao();
        $this->initBean();
        $this->initTest();
    }

    private function destPathProcess($dest, $file)
    {
        $dest = str_replace('php.manage', 'php', $dest);

        return $dest;
    }

    private function passwd($args)
    {
        if (!$username = Z::arrayGet($args, ["-u", "u"])) {
            $this->error("Username cannot be empty, please use -u {username}", "", true);
        }
        if (!$newPassword = Z::arrayGet($args, ["-p", "p"])) {
            $this->error("Password cannot be empty, please use -u {password}", "", true);
        }
        $UserDao = new UserDao();
        $where   = ['username' => $username];
        $id      = $UserDao->findCol('id', $where);
        if (!$id) {
            return '用户不存在';
        }
        $data = [];
        /** @var StrUtils $StrUtils */
        $StrUtils            = z::extension('Action\StrUtils');
        $data['key']         = $StrUtils->randString(4, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $data['password']    = $UserDao->encryptPassword($newPassword, $data['key']);
        $data['update_time'] = date('Y-m-d H:i:s');
        if ($UserDao->update($data, $id)) {
            $this->success("Password update succeeded");
        } else {
            $this->success("Password update failed");
        }
    }
}
