<?php

namespace callmez\wechat;

use Yii;
use yii\caching\TagDependency;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\models\Module as ModuleModel;

// 定义毫秒时间戳
defined('TIMESTAMP') or define('TIMESTAMP', (int) YII_BEGIN_TIME);
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
     * 核心模块存放路径
     */
    const CORE_MODULE_PATH = '@callmez/wechat/modules';
    /**
     * 扩展模块存放路径
     */
    const ADDON_MODULE_PATH = '@app/modules/wechat/modules';
    /**
     * 扩展模块缓存
     */
    const CACHE_ADDON_MODULES_KEY = 'wechat_addon_modules_cache';
    /**
     * 模块的名称
     * @var string
     */
    public $name = '微信';
    /**
     * 扩展模块存储类, 必须继承callmez\wechat\models\Module
     * @var string
     */
    public $moduleModelClass = 'callmez\wechat\models\Module';
    /**
     * 菜单存储类, 必须继承class\wechat\models\Menu
     * @var string
     */
    public $menuModelClass = 'class\wechat\models\Menu';
    /**
     * 模块控制器的命名空间
     * @var string
     */
    public $controllerNamespace = 'callmez\wechat\controllers';

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = array_merge([
            'modules' => array_merge($this->addonModules(), $this->defaultModules())
        ], $config);
        parent::__construct($id, $parent, $config);
    }

    /**
     * 默认模块
     * @var array
     */
    public function defaultModules()
    {
        return [
            'admin' => ['class' => 'callmez\wechat\modules\admin\Module'], // 后台管理
        ];
    }

    /**
     * 微信扩展模块
     * @return array
     */
    public function addonModules()
    {
        $cache = Yii::$app->cache;
        if (($modules = $cache->get(self::CACHE_ADDON_MODULES_KEY)) === false) {
            $class = $this->moduleModelClass;
            $modules = array_map(function($model) {
                return [
                    'class' => $model->getModuleBaseNamespace() . '\Module',
                    'name' => $model->name,
                ];
            }, $class::find()->indexBy('id')->all());
            $cache->set(self::CACHE_ADDON_MODULES_KEY, $modules, null, new TagDependency([
                'tags' => [ModuleModel::CACHE_DATA_DEPENDENCY_TAG]
            ]));
        }
        return $modules;
    }

    /**
     * 模块设计器的名字
     *
     * gii的微信generator配置:
     * ```
     *    'generators' => [
     *     [...]
     *     'wechat' => [
     *          'class' => 'callmez\wechat\generators\wechat\Generators'
     *     ],
     *     [...]
     *     ]
     * ```
     *
     * @var string
     */
    public $giiGeneratorName = 'wechat';
    /**
     * gii模块名称
     * @var string
     */
    public $giiModuleName = 'gii';

    /**
     * 模块设计器是否可用
     * @return bool
     */
    public function canCreateModule()
    {
        $gii = Yii::$app->getModule($this->giiModuleName);
        return $gii !== null && isset($gii->generators[$this->giiGeneratorName]);
    }
}
