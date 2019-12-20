<?php

namespace Zls\Manage\Command;

use Dao\ZlsManage\UserDao;
use Z;
use Zls\Action\StrUtils;

trait Replace
{
    public function backToSource()
    {
        $this->force = true;
        $this->backToSourceDatabase();
        $this->backToSourceView();
        $this->backToSourceBusiness();
        $this->backToSourceController();
        $this->backToSourceDao();
        $this->backToSourceBean();
        $this->backToSourceTest();
    }

    private function backToSourceTest()
    {
        $source = Z::realPath('./tests' . '/ZlsManage', true, false);
        $dest   = Z::realPath(__DIR__ . '/../Tests' . '/ZlsManage', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Tests success');
    }

    private function backToSourceBean()
    {
        $source = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBeanDirName() . '/ZlsManage', true);
        $dest   = Z::realPath(__DIR__ . '/../Bean' . '/ZlsManage', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Bean success');
    }

    private function backToSourceDao()
    {
        $source = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getDaoDirName() . '/ZlsManage', true);
        $dest   = Z::realPath(__DIR__ . '/../Dao' . '/ZlsManage', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Dao success');
    }

    private function backToSourceController()
    {
        $source = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getControllerDirName() . '/ZlsManage', true);
        $dest   = Z::realPath(__DIR__ . '/../Controller' . '/ZlsManage', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Controller success');
    }

    private function backToSourceBusiness()
    {
        $source = Z::realPath(ZLS_APP_PATH . 'classes/' . Z::config()->getBusinessDirName() . '/ZlsManage', true);
        $dest   = Z::realPath(__DIR__ . '/../Business/ZlsManage', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Business success');
    }

    private function backToSourceView()
    {
        $source = Z::realPathMkdir('./zls-manage', true, false, true);
        $dest   = Z::realPath(__DIR__ . '/../View', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('View success');
    }

    private function backToSourceDatabase()
    {
        $source = Z::realPathMkdir('./database', true, false, false);
        $dest   = Z::realPath(__DIR__ . '/../Database', true, false);
        $this->batchCopy($source, $dest, $this->force, function ($dest, $file) {
            return $this->backToSourceDestPathProcess($dest, $file);
        });
        $this->success('Database success');
    }


    private function backToSourceDestPathProcess($dest, $file)
    {
        $dest = str_replace('php', 'php.manage', $dest);

        return $dest;
    }
}
