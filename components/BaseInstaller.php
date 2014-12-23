<?php
namespace callmez\wechat\components;

use yii\base\Object;

/**
 * 微信扩展模块安装基类. 微信扩展模块安装程序必须继承此类
 * @package callmez\wechat\components
 */
abstract class BaseInstaller extends Object
{
    abstract public function install();
    abstract public function uninstall();
}