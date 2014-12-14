<?php

namespace callmez\wechat;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use callmez\wechat\components\BaseModule;
use callmez\wechat\models\Module as ModuleModel;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'callmez\wechat\controllers';

    /**
     * 微信请求接收器类设置
     * @var string|array
     */
    public $receiver = 'callmez\wechat\components\Receiver';
    /**
     * 微信请求接收器触发的Route ID
     * @var string
     */
    public $receiverRouterId = 'api';

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
                'class' => $this->getModuleNamespace($name) . '\Module',
                'model' => $data['id']
            ];
        }
        return $modules;
    }

    /**
     * 获取微信扩展模块命名空间
     * @param $name
     * @return string
     */
    public static function getModuleNamespace($name)
    {
        if (Yii::$app->hasModule($_name = ($pos = strpos($name, '/')) !== false ? substr($name, 0, $pos) : $name)) {
            $namespace = Yii::$app->getModule($_name)->controllerNamespace . '\\modules\\wechat\\modules';
            if ($pos !== false) {
                $namespace .= '\\' . substr($name, $pos + 1);
            }
        } else {
            $namespace = 'app\\modules\\wechat\\modules\\' . $name;
        }
        return rtrim(str_replace('/', '\\', $namespace), '\\');
    }
}
