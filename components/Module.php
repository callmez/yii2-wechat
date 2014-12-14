<?php
namespace callmez\wechat\components;

class Module extends \yii\base\Module
{
    /**
     * 微信扩展模块数据表数据Model类
     * @var int|object
     */
    public $_model;

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        if (!is_object($this->_model)) {
            $this->setModel(\callmez\wechat\models\Module::findOne($this->_model));
            if ($this->_model === null) {
                throw new InvalidConfigException('The wechat sub-module must set.');
            }
        }
        return $this->_model;
    }
}
