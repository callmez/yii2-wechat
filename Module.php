<?php

namespace callmez\wechat;

use Yii;

// 定义毫秒时间戳
defined('TIMESTAMP') or define('TIMESTAMP', (int)YII_BEGIN_TIME);
// 自动注册存储目录(可以在config中配置)
isset(Yii::$aliases['@storage']) or Yii::setAlias('@storage', Yii::getAlias('@web/storage'));
isset(Yii::$aliases['@storageRoot']) or Yii::setAlias('@storageRoot', Yii::getAlias('@webroot/storage'));

/**
 * 微信模块
 * @package callmez\wechat
 */
class Module extends \yii\base\Module
{
    /**
     * 模块的名称
     * @var string
     */
    public $name = '微信';
    /**
     * 模块控制器的命名空间
     * @var string
     */
    public $controllerNamespace = 'callmez\wechat\controllers';
}
