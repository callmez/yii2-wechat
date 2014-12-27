<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\models\Module;

/**
 * 扫描可用的微信扩展模块
 * @package callmez\wechat\components
 */
class ModuleDiscovery extends Object
{
    /**
     * 扫描的目录
     * @var string
     */
    private $_directory;

    private $_wechatModules = [];

    private $_modules = [];

    public $directoryAlias = '@app/modules';

    public function init()
    {
        if (!Yii::getRootAlias($this->directoryAlias)) {
            throw new InvalidConfigException('The "directoryAlias" property is not available alias.');
        }
        $this->_directory = Yii::getAlias($this->directoryAlias);
    }

    /**
     * 扫描可用模块
     * 该扩展只会扫描两个路径
     * 微信扩展模块: $this->getDirectory()/wechat/modules/*
     * 专用模块微信扩展: $this->getDirectory()/{module}/modules/wechat (优先级会高于微信扩展模块)
     */
    public function scan()
    {
        $this->scanDirectory();
        return array_merge($this->_wechatModules, $this->_modules);
    }

    /**
     * 扫描目录
     */
    protected function scanDirectory()
    {
        $callback = function($file, $wechat = true) {
            if ($wechat) {
                $type = '_wechatModules';
                $path = "/wechat/modules/{$file}";
            } else {
                $type = '_modules';
                $path = "/{$file}/modules/wechat";
            }
            $modulePath = $this->getAbsolutePath($path);
            $configFile = $modulePath . '/' . ModuleInstaller::$configFileName;
            if (!file_exists($configFile) || !class_exists($class = $this->getModuleClass($path))) {
                return ;
            }
            $this->{$type}[$file] = array_merge(Module::$default, Yaml::parse(file_get_contents($configFile)), [
                'path' => $modulePath,
                'class' => $class
            ]);
        };
        $this->readDirectory($this->_directory, function($file) use ($callback) {
            if ($file == 'wechat') {
                $this->readDirectory($this->_directory . '/wechat/modules/', $callback);
            } else {
                $callback($file, false);
            }
        });
    }

    /**
     * 读取模块目录文件列表
     * @param $dir
     * @param callable $callback
     */
    protected function readDirectory($dir, \Closure $callback)
    {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $callback($file);
        }
        closedir($handle);
    }

    protected function getAbsolutePath($path)
    {
        return $this->_directory . $path;
    }

    /**
     * 根据相对路径(例: /wechat/modules/test)获取扩展模块Module文件的class namespace
     * 微信扩展模块Module文件名必须为Module.php, Class名必须为Module
     * @param $path
     * @return string
     */
    protected function getModuleClass($path)
    {
        $namespace = ltrim($this->directoryAlias . $path . '/Module', '@');
        return str_replace('/', '\\', $namespace);
    }
}