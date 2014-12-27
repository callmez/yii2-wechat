<?php
namespace callmez\wechat\components;

use callmez\wechat\models\Module;

/**
 * 微信扩展模块基类, 微信扩展模块必须继承此类
 * @package callmez\wechat\components
 */
abstract class BaseModule extends \yii\base\Module
{
    /**
     * 微信扩展模块数据表数据Model类
     * @var int|object
     */
    public $_model;

    public function setModel(Module $model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        if ($this->_model === null) {
            throw new InvalidConfigException('The wechat sub-module must set.');
        }
        return $this->_model;
    }
}
