<?php
namespace callmez\wechat\components;

use Yii;
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

    public function setDirectory($directory)
    {
        $this->_directory = Yii::getAlias($directory);
    }

    public function getDirectory()
    {
        if ($this->_directory === null) {
            $this->setDirectory('@app/modules');
        }
        return $this->_directory;
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
                $path = "{$this->getDirectory()}/wechat/modules/{$file}";
            } else {
                $type = '_modules';
                $path = "{$this->getDirectory()}/{$file}/modules/wechat";
            }
            $configFile = $path . '/' . ModuleInstaller::$configFileName;
            if (!file_exists($configFile)) {
                return ;
            }
            $this->{$type}[$file] = array_merge(Module::$default, Yaml::parse(file_get_contents($configFile)), [
                'path' => $path
            ]);
        };
        $this->readDirectory($this->getDirectory(), function($file) use ($callback) {
            if ($file == 'wechat') {
                $this->readDirectory($this->getDirectory() . '/wechat/modules/', $callback);
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
}