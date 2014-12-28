<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Object;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\models\Module;
use yii\helpers\ArrayHelper;

/**
 * 扫描可用的微信扩展模块
 * @package callmez\wechat\components
 */
class ModuleDiscovery extends Object
{
    /**
     * 默认模块级别排序
     * @var array
     */
    protected $sortKey = [
        'module' => 1, // 普通模块微信扩展
        'wechat' => 2, // 微信扩展模块
        'system' => 3 // 系统模块
    ];
    /**
     * 扫描的目录列表设置
     * @var array
     */
    private $_directories = [
        'module' => [
            'alias' => '@app/modules',
            'whitelist' => [],
            'blacklist' => ['wechat'],
        ],
        'system' => [
            'alias' => '@callmez/wechat/modules',
            'whitelist' => [],
            'blacklist' => [],
        ],
        'wechat' => [
            'alias' => '@app/modules/wechat/modules',
            'whitelist' => [],
            'blacklist' => [],
        ],
    ];
    /**
     * 当前正在扫描的目录的分类名
     * @var string
     */
    protected $name;
    /**
     * 当前正在扫描的目录别名
     * @var string
     */
    protected $alias;
    /**
     * 当前正在扫描的目录
     * @var string
     */
    protected $directory;
    /**
     * 扫描目录白名单
     * @var array
     */
    protected $whitelist = [];
    /**
     * 扫描目录黑名单
     * @var array
     */
    protected $blacklist = [];
    /**
     * 符合要求的模块
     * @var array
     */
    protected $modules = [];

    /**
     * 设置扫描目录
     */
    public function setDirectories(array $directories)
    {
        $this->_directories = array_merge($this->_directories, $directories);
        foreach ($this->_directories as $name => &$directory) {
            if (!isset($directory['alias'])) {
                throw new InvalidParamException("The {$name}['alias'] setting of 'directories' property must set.");
            }
            !isset($directory['whitelist']) && $directory['whitelist'] = [];
            !isset($directory['blacklist']) && $directory['blacklist'] = [];
        }
    }

    public function getDirectories()
    {
        return $this->_directories;
    }

    /**
     * 加载指定目录设置
     * @param $settings
     */
    protected function loadSettings($settings)
    {
        $this->alias = $settings['alias'];
        $this->directory = Yii::getAlias($this->alias);
        $this->whitelist = $settings['whitelist'];
        $this->blacklist = $settings['blacklist'];
    }

    /**
     * 扫描指定目录
     * @return array
     */
    public function scan()
    {
        foreach ($this->getDirectories() as $name => $settings) {
            $this->name = $name;
            $this->loadSettings($settings);

            $this->modules[$this->name] = [];
            $this->scanDirectory('', function($file) {
                $module = $this->readDirectory($file, $this->name == 'module' ? 'modules/wechat' : null);
                if ($module) {
                    $this->modules[$this->name][$module['module']] = $module;
                }
            });
        }
        return $this->mergeModules();
    }

    /**
     * 按照模块级别排序并合并
     * @return array
     */
    protected function mergeModules()
    {
        uksort($this->modules, function($a, $b) { // 按照优先级排序
            if(!isset($this->sortKey[$a])) {
                $this->sortKey[$a] = 0;
            }
            if(!isset($this->sortKey[$b])) {
                $this->sortKey[$b] = 0;
            }
            return $this->sortKey[$a] > $this->sortKey[$b] ? 1 : -1;
        });
        return (array)array_reduce($this->modules, function($modules, $_modules) {
            return array_merge((array)$modules, $_modules);
        });
    }

    /**
     * 扫描目录
     * @param $path
     * @param $callback
     */
    protected function scanDirectory($path, $callback)
    {
        $dir = $this->directory . '/' . $path;
        if (!is_dir($dir)) {
            return ;
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if (
                in_array($file, ['.', '..']) ||
                !is_dir($dir . '/' . $file) ||
                (!empty($this->whitelist) && !in_array($file, $this->whitelist)) ||
                (!empty($this->blacklist) && in_array($file, $this->blacklist))
            ) {
                continue;
            }
            is_callable($callback) && call_user_func_array($callback, [$file]);
        }
        closedir($handle);
    }

    /**
     * 读取模块目录设置
     * @param $file
     * @param null $path
     * @return array
     */
    protected function readDirectory($file, $path = null)
    {
        $path = trim($file . '/' . $path, '/');
        $modulePath = $this->getAbsolutePath($path);
        $configFile = $modulePath . '/' . ModuleInstaller::$configFileName;
        if (file_exists($configFile) && class_exists($class = $this->getModuleClass($path))) {
            $module = array_merge(Module::$default, Yaml::parse(file_get_contents($configFile)), [
                'path' => $modulePath,
                'class' => $class
            ]);
            if (isset($module['module'])) {
                return $module;
            }
        }
    }

    /**
     * 获取目录的绝对路径
     * @param $path
     * @return string
     */
    protected function getAbsolutePath($path)
    {
        return $this->directory . '/' . $path;
    }

    /**
     * 根据相对路径(例: /wechat/modules/test)获取扩展模块Module文件的class namespace
     * 微信扩展模块Module文件名必须为Module.php, Class名必须为Module
     * @param $path
     * @return string
     */
    protected function getModuleClass($path)
    {
        $namespace = ltrim($this->alias . '/' . $path . '/Module', '@');
        return str_replace('/', '\\', $namespace);
    }
}