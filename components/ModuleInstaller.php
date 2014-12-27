<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Object;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use callmez\wechat\models\Module;

/**
 * 微信扩展模块安装器
 * @package callmez\wechat\components
 */
class ModuleInstaller extends Object
{
    /**
     * 微信扩展模块配置文件名
     * @var string
     */
    public static $configFileName = 'wechat.yml';
    private $_model;
    private $_modulePath;
    private $_moduleNamespace;

    public function setModel(Module $model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        if ($this->_model === null) {
            $this->setModel(new Module());
        }
        return $this->_model;
    }

    public function setModulePath($path)
    {
        $this->_modulePath = $path;
    }

    public function getModulePath()
    {
        if ($this->_modulePath === null) {
            $this->setPath(Yii::getAlias('@' . str_replace('\\', '/', $this->getModuleNamespace())));
        }
        return $this->_modulePath;
    }

    public function setModuleNamespace($namespace)
    {
        $this->_moduleNamespace = $namespace;
    }

    public function getModuleNamespace()
    {
        if ($this->_moduleNamespace === null) {
            if (!Yii::$app->hasModule('wechat')) {
                throw new InvalidCallException('The "wechat" module must be set.');
            } elseif (!($namespace = Yii::$app->getModule('wechat')->getModuleNamespace($this->getModel()->module, true))) {
                throw new InvalidParamException("The module 'wechat' sub-module '{$this->getModel()->module}' is missing.");
            }
            $this->setModuleNamespace($namespace);
        }
        return $this->_moduleNamespace;
    }

    /**
     * 扩展模块安装
     * @param array $moduleConfig
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function install()
    {
        $model = $this->getModel();
        if (!$model->getIsNewRecord()) { // 模块表必须为新建记录
            throw new InvalidConfigException("The 'model' class must be new record class in method 'install'.");
        }
        if ($model->validate()) {
            if ($installer = $this->getModuleInstaller()) {
                $installer->install();
            }
            return $model->save(false);
        }
        return false;
    }

    /**
     * 扩展模块卸载
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function uninstall()
    {
        $model = $this->getModel();
        if ($model->getIsNewRecord()) { // 模块表必须为新建记录
            throw new InvalidConfigException("The 'model' class must not be new record class in method 'uninstall'.");
        }
        if ($installer = $this->getModuleInstaller()) {
            $installer->uninstall();
        }
        return $model->delete();
    }

    /**
     * 检查扩展模块是否有自定义模块安装器
     * @return bool
     */
    protected function getModuleInstaller()
    {
        $class = $this->getModuleNamespace() . '\Installer';
        if (class_exists($class)) {
            $installer = Yii::createObject([
                'class' => $class
            ]);
            if (!($installer instanceof BaseInstaller)) {
                throw new InvalidCallException("The wechat sub module '{$this->getModel()->module}' must be an instance of '" . BaseInstaller::className() . "'");
            }
            return $installer;
        }
        return false;
    }
}