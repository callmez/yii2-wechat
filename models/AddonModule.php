<?php

namespace callmez\wechat\models;

use Yii;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\caching\TagDependency;
use yii\base\NotSupportedException;
use yii\base\InvalidParamException;
use yii\behaviors\TimestampBehavior;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\Module;
use callmez\wechat\behaviors\EventBehavior;

/**
 * This is the model class for table "{{%wechat_addon_module}}".
 *
 */
class AddonModule extends \yii\db\ActiveRecord
{
    /**
     * 核心模块
     */
    const TYPE_CORE = 'core';
    /**
     * 微信扩展模块
     */
    const TYPE_ADDON = 'addon';
    /**
     * 迁移安装
     */
    const MIGRATION_INSTALL = 'install';
    /**
     * 迁移卸载
     */
    const MIGRATION_UNINSTALL = 'uninstall';
    /**
     * 迁移升级
     */
    const MIGRATION_UPGRADE = 'upgrade';
    /**
     * @var array
     */
    public static $types = [
        self::TYPE_CORE => '核心模块',
        self::TYPE_ADDON => '扩展模块',
    ];

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at'
                ]
            ],
            'event' => [
                'class' => EventBehavior::className(),
                'events' => [
                    ActiveRecord::EVENT_BEFORE_DELETE => function($event) { // 是否能删除
                        $event->isValid = $this->getCanUninstall(true);
                    },
                    // 数据库变动必须更新缓存, 并执行相关迁移脚本
                    ActiveRecord::EVENT_AFTER_INSERT => function($event) {
                        $this->updateCache();
                        $this->migration(self::MIGRATION_INSTALL);
                    },
                    ActiveRecord::EVENT_AFTER_DELETE => function($event) {
                        $this->updateCache();
                        $this->migration(self::MIGRATION_UNINSTALL);
                    },
                    ActiveRecord::EVENT_AFTER_UPDATE => function() {
                        $this->updateCache();
                        $this->migration(self::MIGRATION_UPGRADE);
                    },
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_addon_module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'version'], 'required'],

            [['id'], 'string', 'max' => 20],
            [['name', 'author'], 'string', 'max' => 50],
            [['version'], 'string', 'max' => 10],
            [['ability'], 'string', 'max' => 100],
            [['site'], 'string', 'max' => 255],

            [['id'], 'unique', 'message' => '模块唯一ID标识已经被使用或者该模块已经安装.'],
            [['migration'], 'boolean'],

            [['type'], 'checkType', 'skipOnEmpty' => false],

            [['description'], 'string'],
            [['description'], 'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '模块ID',
            'name' => '模块名称',
            'type' => '模块类型',
            'version' => '模块版本',
            'ability' => '模块功能简述',
            'description' => '模块详细描述',
            'author' => '模块作者',
            'site' => '模块详情地址',
            'migration' => '是否有迁移数据',
            'created_at' => '安装时间'
        ];
    }

    /**
     * 更新列表数据缓存(模块调用已安装模块缓存)
     * @see callmez\wechat\Module::addonModules()
     * @param $cacheKey
     */
    protected function updateCache($cacheKey = Module::ADDON_MODULE_LIST_CACHE_NAME)
    {
        $cache = Yii::$app->get(static::getDb()->queryCache, false);
        if ($cache instanceof Cache) {
            TagDependency::invalidate($cache, $cacheKey);
        }
    }

    /**
     * 执行迁脚本
     * @param $type
     */
    protected function migration($type)
    {
        // TODO 模块迁移功能完善
        switch ($type) {
            case self::MIGRATION_INSTALL:
            case self::MIGRATION_UNINSTALL:
            case self::MIGRATION_UPGRADE:
                return;
            default:
                throw new InvalidParamException("Migration type '{$type}' does not support.");
        }
    }

    /**
     * 是否已安装的模块
     * @return bool
     */
    public function getIsInstalled()
    {
        return !$this->getIsNewRecord();
    }

    /**
     * 核心模块不能卸载
     * @param bool $setError
     * @return bool
     */
    public function getCanUninstall($setError = false)
    {
        if ($this->type == self::TYPE_CORE) {
            $setError && $this->addError('type', '核心模块不能卸载');
            return false;
        }
        return true;
    }

    /**
     * 安装模块
     * @param bool $runValidation
     * @return bool
     */
    public function install($runValidation = true)
    {
        return parent::save($runValidation);
    }

    /**
     * 卸载模块
     * @return false|int
     */
    public function uninstall()
    {
        return parent::delete();
    }

    /**
     * 根据模块的存放路径判断模块的类型
     */
    public function checkType($attribute, $params)
    {
        if (file_exists(Yii::getAlias(Module::CORE_MODULE_PATH . '/' . $this->id . '/wechat.yml'))) {
            $this->$attribute = self::TYPE_CORE;
        } else {
            $this->$attribute = self::TYPE_ADDON;
        }
    }

    /**
     * 获取扩展模块的类名
     * 注意: 所有微信的扩展模块必须使用Module类名(文件名也为Module.php)为模块的启动文件
     * @return string
     */
    public function getModuleClass()
    {
        $path = $this->type == self::TYPE_ADDON ? Module::ADDON_MODULE_PATH : Module::CORE_MODULE_PATH;
        return str_replace('/', '\\', ltrim($path, '@')) . '\\' . $this->id . '\\Module';
    }

    /**
     * 根据ID查找可用的模块
     * @param $id
     * @return null
     */
    public static function findAvailableModuleById($id)
    {
        $availableModels = static::findAvailableModules();
        return array_key_exists($id, $availableModels) ? $availableModels[$id] : null;
    }

    /**
     * 查找可用的插件模块
     * @return mixed
     */
    public static function findAvailableModules()
    {
        return static::scanAvailableModules([Module::ADDON_MODULE_PATH, Module::CORE_MODULE_PATH]);
    }

    /**
     * 扫描可用的插件模块
     *
     * 该方法是严格按照Yii的模块路径规则(比如@app/modules, @app/mdoules/example/modules)来查找模块
     * 如果您的模块有特殊路径需求. 可能正确安装不了, 建议按照规则设计扩展模块
     *
     * @param array|string $paths
     * @return array
     */
    protected static function scanAvailableModules($paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        $modules = [];
        foreach ($paths as $path) {
            $path = Yii::getAlias($path);
            if (is_dir($path) && (($handle = opendir($path)) !== false)) {
                while (($file = readdir($handle)) !== false) {
                    if (in_array($file, ['.', '..']) || !is_dir($currentPath = $path . DIRECTORY_SEPARATOR . $file)) {
                        continue;
                    }
                    // 是否有wechat.yml安装配置文件
                    $settingFile = $currentPath . DIRECTORY_SEPARATOR . 'wechat.yml';
                    if (file_exists($settingFile)) {
                        $model = new static;
                        $model->setAttributes(Yaml::parse(file_get_contents($settingFile)));
                        if ($model->id == $file && $model->validate()) { // 模块名必须等于目录名并且验证模块正确性
                            $modules[$model->id] = $model;
                        }
                    }
                }
            }
        }
        return $modules;
    }
}
