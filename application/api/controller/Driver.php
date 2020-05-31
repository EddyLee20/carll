<?php

namespace app\api\controller;

use app\common\traits\ApiHelper;

/**
 * 访问控制模块驱动
 * Class driver
 * @package app\common\library\sms
 */
class Driver
{
    private $config;    // 配置信息
    private $engine;    // 当前短信引擎类
    private $engineName;    // 当前短信引擎名称

    /**
     * 构造方法
     * Driver constructor.
     * @param $config
     */
    public function __construct($config)
    {
        // 配置信息
        $this->config = $config;
        // 当前引擎名称
        $this->engineName = $config['class'];
        // 实例化当前存储引擎
        $this->engine = $this->getEngineClass();
    }

    /**
     * 访问控制器方法
     * @param $func
     * @return mixed
     */
    public function getFunc($func)
    {
        return $this->engine->$func();
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->engine->getError();
    }

    /**
     * 获取当前的存储引擎
     * @return array
     */
    private function getEngineClass()
    {
        $classSpace = __NAMESPACE__ . '\\action\\' . ucfirst($this->engineName);
        if (!class_exists($classSpace)) {
            return ApiHelper::output(20000, '未找到存储引擎类: ' . $this->engineName);
        }
        return new $classSpace($this->config['User']);
    }

}
