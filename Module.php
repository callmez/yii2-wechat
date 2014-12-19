<?php

namespace callmez\wechat;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use callmez\wechat\components\BaseModule;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\models\Module as ModuleModel;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'callmez\wechat\controllers';

    public function init()
    {
        parent::init();
        $this->setModules(array_merge($this->loadModules(), $this->getModules()));
    }

    public function setModule($id, $module)
    {
        if ($module !== null && !($module instanceof BaseModule)) {
            throw new InvalidConfigException('The wechat sub-module must be instance of "' . BaseModule::className() . '"');
        }
        parent::setModule($id, $module);
    }

    /**
     * 从数据库加载模块数据
     */
    public function loadModules()
    {
        $modules = [];
        foreach(ModuleModel::find()->select(['id', 'name'])->indexBy('name')->asArray()->each(10) as $name => $data) {
            $modules[$name] = [
                'class' => ModuleHelper::getWechatModuleNamespace($name) . '\Module',
                'model' => $data['id']
            ];
        }
        return $modules;
    }
}
