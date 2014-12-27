<?php

namespace callmez\wechat;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use callmez\wechat\components\BaseModule;
use callmez\wechat\components\ModuleDiscovery;
use callmez\wechat\models\Module as ModuleModel;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'callmez\wechat\controllers';

    public function init()
    {
        parent::init();

        $this->loadModules();
    }

    /**
     * 微信扩展模块必须继承BaseModule
     * @param string $id
     * @param array|null|\yii\base\Module $module
     * @throws \yii\base\InvalidConfigException
     */
    public function setModule($id, $module)
    {
        if ($module !== null && !($module instanceof BaseModule)) {
            throw new InvalidConfigException('The wechat sub-module must be instance of "' . BaseModule::className() . '"');
        }
        parent::setModule($id, $module);
    }

    /**
     * 加载已安装的微信扩展模块
     */
    public function loadModules()
    {
        $modules = array_map(function($module) {
            return [
                'class' => $module->class,
                'model' => $module
            ];
        }, $this->getInstalledModules());

        $this->setModules(array_merge($modules, $this->getModules()));
    }

    private $_availableModules;
    /**
     * 获取可用的微信扩展模块数据
     * @return mixed
     */
    public function getAvailableModules()
    {
        if ($this->_availableModules === null) {
            $this->_availableModules = Yii::createObject([
                'class' => ModuleDiscovery::className()
            ])->scan();
        }
        return $this->_availableModules;
    }

    private $_installedModules;
    /**
     * 获取已安装的微信扩展模块数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalledModules()
    {
        if ($this->_installedModules === null) {
            $this->_installedModules = ModuleModel::find()->indexBy('module')->all();
        }
        return $this->_installedModules;
    }

    /**
     * 获取微信扩展模块Module文件的namespace
     * @param $id
     * @param bool $includeAvailableModules 是否包含可用扩展模块
     * @return bool
     */
    public function getModuleNamespace($id, $includeAvailableModules = false)
    {
        $namespace = null;
        if (($modules = $this->getModules()) && isset($modules[$id])) {
            if (!is_array($modules[$id])) {
                $namespace = $modules[$id];
            } elseif (isset($modules[$id]['class'])) {
                $namespace = $modules[$id]['class'];
            }
        } elseif ($includeAvailableModules) { // 可用模块路径
            $modules = $this->getAvailableModules();
            isset($modules[$id]) && isset($modules[$id]['class']) && $namespace = $modules[$id]['class'];
        }
        return $namespace;
    }
}
