<?php

namespace callmez\wechat;

use Yii;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\models\AddonModule;
use yii\caching\TagDependency;

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
     * 微信扩展模块数据缓存
     */
    const ADDON_MODULE_LIST_CACHE_NAME = 'wechat_addon_module_list_cache';
    /**
     * 模块的名称
     * @var string
     */
    public $name = '微信';
    /**
     * 扩展模块存放路径
     * @var string
     */
    public $addonModuleClass = 'callmez\wechat\models\AddonModule';
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
            'modules' => array_merge($this->addonModules(), $this->coreModules())
        ], $config);
        parent::__construct($id, $parent, $config);
    }

    /**
     * 核心模块
     * @return array
     */
    public function coreModules()
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
        $class = $this->addonModuleClass;
        $models = $class::getDb()->cache(function ($db) use($class) {
            return $class::find()
                ->andWhere(['type' => [AddonModule::TYPE_CORE, AddonModule::TYPE_ADDON]])
                ->indexBy('id')
                ->all();
        }, null, new TagDependency(['tags' => [self::ADDON_MODULE_LIST_CACHE_NAME]]));
        return array_map(function($model) {
            return ['class' => $model->getModuleClass()];
        }, $models);
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
    public function canDesignAddonModule()
    {
        $gii = Yii::$app->getModule($this->giiModuleName);
        return $gii !== null && isset($gii->generators[$this->giiGeneratorName]);
    }
}
