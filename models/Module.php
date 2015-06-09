<?php

namespace callmez\wechat\models;

use callmez\wechat\assets\WechatAsset;
use Yii;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\caching\TagDependency;
use yii\base\InvalidCallException;
use yii\base\NotSupportedException;
use yii\base\InvalidParamException;
use yii\behaviors\TimestampBehavior;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\Module as WechatModule;

/**
 * 微信扩展模块类
 * @package callmez\wechat\models
 */
class Module extends ActiveRecord
{
    /**
     * 模块数据缓存
     */
    const CACHE_MODULES_DATA = 'cache_wechat_model_moduels_data';
    /**
     * 模块数据缓存依赖标签
     */
    const CACHE_MODULES_DATA_DEPENDENCY_TAG = 'cache_wechat_model_moduels_data_tag';
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
            'timestamp' => TimestampBehavior::className()
        ];
    }

    /**
     * 禁用事务(便于执行migration)
     * @return array
     */
    final public function transactions()
    {
        return [];
    }

    /**
     * 加入安装和升级migration操作, 使用事务(self::transactions()禁止默认事务)来控制数据一致性
     * 并更新数据缓存
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $transaction = static::getDb()->beginTransaction();
        try {

            if ($this->getIsNewRecord()) {
                $migrationType = self::MIGRATION_INSTALL;
                $result = $this->insert($runValidation, $attributeNames);
            } else {
                $migrationType = self::MIGRATION_UPGRADE;
                $result = $this->update($runValidation, $attributeNames) !== false;
            }
            if ($result !== false && $this->migration) {
                $result = $this->migration($migrationType);
            }

            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();

                static::updateCache();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * 加入卸载migration操作, 使用事务(self::transactions()禁止默认事务)来控制数据一致性
     * 并更新数据缓存
     * @inheritdoc
     */
    public function delete()
    {
        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->deleteInternal();

            if ($result !== false && $this->migration) { // 有迁移执行迁移脚本
                $result = $this->migration(self::MIGRATION_UNINSTALL);
            }

            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();

                static::updateCache();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'name', 'category', 'version'], 'required'],

            [['id'], 'string', 'max' => 20],
            [['name', 'author'], 'string', 'max' => 50],
            [['version'], 'string', 'max' => 10],
            [['ability'], 'string', 'max' => 100],
            [['site'], 'string', 'max' => 255],

            [['id'], 'match', 'pattern' => '/^[\w]+$/', 'message' => '模块唯一ID标识只能包含英文字母,数字和_符号'], // 模块ID哟福
            [['id'], 'unique', 'message' => '模块唯一ID标识已经被使用或者该模块已经安装.'],
            [['admin', 'migration', 'reply_rule'], 'boolean'],

            [['type'], 'checkType', 'skipOnEmpty' => false],
            [['category'], 'in', 'range' => array_keys(Yii::$app->getModule('wechat')->getCategories()), 'skipOnEmpty' => false],

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
            'id' => 'ID',
            'name' => '名称',
            'type' => '类型',
            'version' => '版本',
            'ability' => '功能简述',
            'description' => '详细描述',
            'author' => '作者',
            'site' => '模块详情地址',
            'admin' => '是否有后台界面',
            'migration' => '是否有迁移数据',
            'category' => '模块所属分类',
            'created_at' => '安装时间',
            'updated_at' => '升级时间'
        ];
    }

    /**
     * 更新列表数据缓存
     * @see callmez\wechat\Module::addonModules()
     * @param $cacheKey
     */
    public static function updateCache($cacheKey = self::CACHE_MODULES_DATA_DEPENDENCY_TAG)
    {
        $cache = Yii::$app->get(static::getDb()->queryCache, false);
        if ($cache instanceof Cache) {
            TagDependency::invalidate($cache, $cacheKey);
        }
    }

    /**
     * 执行模块迁移(安装,升级,卸载)脚本
     * @param $type
     * @return bool
     */
    protected function migration($type)
    {
        $class = ModuleHelper::getBaseNamespace($this) . '\migrations\WechatMigration';
        if (!class_exists($class)) {
            // 卸载如果迁移文件不存在也直接返回迁移成功(防止卸载失败)
            if ($type == self::MIGRATION_UNINSTALL) {
                return true;
            }
            $this->addError('migration', '模块迁移文件不存在');
            return false;
        }
        $migration = Yii::createObject([
            'class' => $class,
            'module' => $this
        ]);
        switch ($type) {
            case self::MIGRATION_INSTALL:
                $result = $migration->up();
                break;
            case self::MIGRATION_UNINSTALL:
                $result = $migration->down();
                break;
            case self::MIGRATION_UPGRADE:
                $result = $migration->to($this->getOldAttribute('version'), $this->getAttribute('version'));
                break;
            default:
                throw new InvalidParamException("Migration type does not support '{$type}'.");
        }
        if ($result === false) {
            $this->addError('migration', '模块迁移脚本执行失败');
            return false;
        }
        return true;
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
     * 安装模块
     * @param bool $runValidation
     * @return bool
     */
    public function install($runValidation = true)
    {
        return $this->save($runValidation);
    }

    /**
     * 卸载模块
     * @return false|int
     */
    public function uninstall()
    {
        return $this->delete();
    }

    /**
     * 根据模块的存放路径判断模块的类型
     */
    public function checkType($attribute, $params)
    {
        // 核心模块的优先级最高
        if (file_exists(Yii::getAlias(WechatModule::CORE_MODULE_PATH . '/' . $this->id . '/wechat.yml'))) {
            $type = self::TYPE_CORE;
        } else {
            $type = self::TYPE_ADDON;
        }
        if ($this->$attribute != $type) {
            return $this->addError($attribute, '模块类型匹配错误');
        }
    }

    /**
     * 模块数据列表缓存
     * 该数据只会返回数组形态模块数据,用于频繁操作(提高效率)的逻辑处理部分
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public static function models()
    {
        $cache = Yii::$app->cache;
        if (($modules = $cache->get(self::CACHE_MODULES_DATA)) === false) {
            $modules = static::find()->indexBy('id')->asArray()->all();
            $cache->set(self::CACHE_MODULES_DATA, $modules, null, new TagDependency([
                'tags' => [self::CACHE_MODULES_DATA_DEPENDENCY_TAG]
            ]));
        }
        return $modules;
    }
}
