<?php

namespace Business\ZlsManage;

use Dao\ZlsManage\LogsDao;
use z;

/**
 * Zls
 * @author        影浅
 * @email         seekwe@gmail.com
 * @copyright     Copyright (c) 2015 - 2017, 影浅, Inc.
 * @link          ---
 * @since         v0.0.1
 * @updatetime    2018-11-08 17:55
 */
class AssistBusiness extends \Zls_Business
{
    const AVATAR_PATH = 'static/avatar';
    const AVATAR_TMP_PATH = 'static/avatar/tmp';

    /**
     * 上传头像图片至临时目录
     * @return array|string
     */
    public function uploadAvatar()
    {
        return $this->upload(self::AVATAR_TMP_PATH, '', ['jpg', 'png'], 'file', 1024);
    }

    /**
     * 移动头像图片
     * @desc 把头像图片从临时目录移动至最终目录并且返回路径
     * @param $path
     * @return string
     */
    public function mvAvatar($path)
    {
        $tmp = Z::urlPath(self::AVATAR_TMP_PATH);
        if (Z::strBeginsWith($path, $tmp)) {
            $newPath = str_replace($tmp, Z::urlPath(self::AVATAR_PATH), $path);
            if (rename(Z::realPath(ltrim($path, '/')), Z::realPath(ltrim($newPath, '/')))) {
                $path = $newPath;
            }
        }

        return $path;
    }

    /**
     * 上传文件
     * @param        $dir
     * @param string $name
     * @param array  $ext
     * @param string $formfield
     * @param int    $size
     * @return array|string
     */
    public function upload($dir, $name = '', $ext = ['jpg', 'png'], $formfield = 'file', $size = 2048)
    {
        /** @var \Zls\Action\FileUp $fileUpload */
        $fileUpload = z::extension('Action\FileUp');
        $fileUpload->setFormField($formfield);
        $fileUpload->setMaxSize($size);
        $fileUpload->setExt($ext);
        $path = $fileUpload->saveFile($name, $dir);
        if (!$path) {
            return z::arrayGet($fileUpload->getError(), 'error');
        }

        return ['url' => z::safePath($path, '', true), 'path' => $path];
    }

    /**
     * 获取日志列表
     * @param     $page
     * @param     $pagesize
     * @param int $unread
     * @param int $type
     * @return array
     */
    public function getLogs($page, $pagesize, $unread = 0, $type = 0)
    {
        $dao   = new LogsDao();
        $where = $unread ? ['status' => $dao::STATUS_NOT] : [];
        if ($type) {
            $where['type'] = $type;
        }
        $lists = $dao->getPage($page, $pagesize, '{page}', '*', $where, ['id' => 'desc']);
        // if ($notReadLists = z::arrayFilter($lists['items'], function ($v) use ($dao) {
        //     return $v['status'] === $dao::STATUS_NOT;
        // })) {
        //     $dao->updateBatch(z::arrayMap(array_values($notReadLists), function ($v) use ($dao) {
        //
        //         return ['id' => $v['id'], 'status' => $dao::STATUS_READ];
        //     }));
        // }
        return $lists;
    }


    /**
     * 获取未读日志总数
     * @param int $lastid
     * @param int $userid
     * @return int
     */
    public function getUnreadMessageCount($lastid = 0, $userid = 0)
    {
        $logsDao = new LogsDao();

        return $logsDao->selectCount(['id >' => $lastid, 'userid' => $userid, 'status' => $logsDao::STATUS_NOT]);
    }

    /**
     * 更新日志状态
     * @param     $ids
     * @param int $uid
     * @return bool
     */
    public function updateMessageStatus($ids, $uid = 0)
    {
        $logsDao = new LogsDao();

        return $logsDao->updateBatch(z::arrayMap($ids, function ($v) use ($logsDao) {
            return ['id' => $v['id'], 'status' => $logsDao::STATUS_READ];
        }));
    }

}