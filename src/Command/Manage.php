<?php

namespace Zls\Manage\Command;

use Z;

class Manage extends \Zls\Command\Command
{
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
            ' init' => [
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
        ];
    }

    /**
     * 命令默认执行.
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
        $dest = Z::realPathMkdir('./zls-manage', true, false, true);
        $source = Z::realPath(__DIR__ . '/../View', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('View success');
    }

    private function initBusiness()
    {
        $dest = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBusinessDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Business', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Business success');
    }

    private function initController()
    {
        $dest = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getControllerDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Controller', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Controller success');
    }

    private function initDao()
    {
        $dest = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getDaoDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Dao', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Dao success');
    }

    private function initBean()
    {
        $dest = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBeanDirName(), true);
        $source = Z::realPath(__DIR__ . '/../Bean', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->destPathProcess($dest, $file);
        });
        $this->success('Bean success');
    }

    private function initDatabase()
    {
        $dest = Z::realPathMkdir('./database', true, false, false);
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
        die;
        $file = ZLS_APP_PATH . 'config/default/swoole.php';
        $originFile = Z::realPath(__DIR__ . '/../Config/swoole.php', false, false);
        $this->copyFile(
            $originFile,
            $file,
            $force,
            function ($status) use ($file) {
                if ($status) {
                    $this->success('config: ' . Z::safePath($file));
                    $this->printStr('Please modify according to the situation');
                } else {
                    $this->error('Profile already exists, or insufficient permissions');
                }
            },
            null
        );
    }

    private function destPathProcess($dest, $file)
    {
        $dest = str_replace('php.manage', 'php', $dest);

        return $dest;
    }
}
