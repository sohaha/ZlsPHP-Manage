<?php
declare(strict_types=1);

namespace Zls\Manage\Utils;

use Z;
use Zls_Business;
use Zls_Dao;

trait ApiController
{
    /** 模块名 @var string */
    protected $module = '';
    /** 处理字段 @var array */
    protected $fields = [];
    /** 排除字段 @var array */
    protected $excludeFields = [];
    /**
     * 预数据
     * @var array
     * 创建时追加到添加数据
     * 更新，删除时追加到过滤条件
     */
    protected $preData = [];

    abstract public function init($requestMethod, $beforeArgs);

    public function before($method, $controller, $args, $methodFull, $class)
    {
        if (method_exists(get_parent_class($this), 'before')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $result = parent::before($method, $controller, $args, $methodFull, $class);
            if (!is_null($result)) {
                return $result;
            }
        }
        $beforeArgs = ['method' => $method, 'controller' => $controller, 'args' => $args, 'methodFull' => $methodFull, 'class' => $class];
        $requestMethod = strtoupper(Z::server('REQUEST_METHOD', ''));
        $result = $this->init($requestMethod, $beforeArgs);
        Z::throwIf(!$this->module, '500', 'Please specify the module first');

        return $result;
    }

    /**
     * 查看
     */
    public function GETzIndex()
    {
        $dao = $this->dao();
        $id = (int)Z::get("id");
        if (!$this->fields) {
            $this->fields = $dao->getReversalColumns();
        }
        if ($id) {
            $item = $dao->find(['id' => $id], false, [], $this->fields);
            if (!$item) {
                return [404, '数据不存在'];
            }
            return [200, '', $item];
        }
        $page = (int)Z::get("page", 1);
        $pagesize = (int)Z::get("pagesize", 10);
        return [200, '', $dao->getPage($page, $pagesize, '', $this->fields, $this->preData, [$dao->getPrimaryKey() => 'desc'])];
    }

    /**
     * 创建
     */
    public function POSTzIndex()
    {
        $data = Z::postJson();
        $err = $this->verifiFilter($data);
        if ($err !== '') {
            return [211, $err];
        }
        if (!$data) {
            return [212, '数据不能为空'];
        }
        $dao = $this->dao();
        $data = array_merge($data, $this->preData);
        $id = (int)$dao->insert($data);
        if (!$id) {
            return [213, '创建失败'];
        }
        return [200, '', ['id' => $id]];
    }

    /**
     * 更新
     */
    public function PUTzIndex()
    {
        $dao = $this->dao();
        $data = Z::postJson();
        $primaryKey = $dao->getPrimaryKey();
        $id = Z::arrayGet($data, $primaryKey);
        $data = $this->excludeData(Z::postJson(), true);
        // 默认只更新传递的参数
        if (!$this->fields) {
            $this->fields = array_keys($data);
        }
        $err = $this->verifiFilter($data);
        if ($err !== '') {
            return [211, $err];
        }
        if (!$data) {
            return [212, '数据不能为空'];
        }
        $where = [$primaryKey => $id];
        $where = array_merge($where, $this->preData);
        $result = (int)$dao->update($data, $where);
        return [200, '', ['result' => $result]];
    }

    /**
     * 删除
     */
    public function DELETEzIndex()
    {
        $data = Z::postJson();
        if (!$this->fields) {
            $this->fields = ['id'];
        }
        $err = $this->verifiFilter($data);
        if ($err !== '') {
            return [211, $err];
        }
        if (!$data) {
            return [212, '条件不能为空'];
        }
        $data = array_merge($data, $this->preData);
        if (!$this->dao()->delete($data)) {
            return [213, '删除失败', $data];
        }
        return [200, '删除成功'];
    }

    /**
     * 排除指定数据
     * @param $data
     * @param bool $excludePrimaryKey
     * @return array
     */
    private function excludeData($data, bool $excludePrimaryKey = false): array
    {
        $fields = $this->excludeFields;
        if ($excludePrimaryKey) {
            array_push($fields, $this->dao()->getPrimaryKey());
        }
        return Z::arrayFilter($data, function ($_, $key) use ($fields) {
            return !in_array($key, $fields, true);
        });
    }

    /**
     * 数据验证
     * @param $data
     * @return string
     */
    protected function verifiFilter(&$data): string
    {
        $dao = $this->dao();
        $data = z::readData($dao->getColumns(), $data, false);
        if (method_exists($dao, 'verifyRules')) {
            $rule = $dao->verifyRules($this->fields);
            $retData = [];
            $errorMsg = '';
            if (!Z::checkData($data, $rule, $retData, $errorMsg)) {
                return $errorMsg;
            }
            $data = $retData;
        }
        return '';
    }

    /**
     * 当前模块 Dao
     * @return Zls_Dao
     */
    protected function dao(): Zls_Dao
    {
        return Z::dao($this->module . 'Dao', true);
    }

    public function after($contents, $methodName, $controllerShort, $args, $methodFull, $class): string
    {
        return is_array($contents) ? Z::json($contents) : Z::json(211, $contents);
    }
}
