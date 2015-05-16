<?php
namespace callmez\wechat\components;

use Yii;
use yii\db\Migration;

/**
 * 微信扩展模块迁移基类
 * Class ModuleMigration
 * @package callmez\wechat\components
 */
class ModuleMigration extends Migration
{
    /**
     * @var callmez\wechat\models\Module
     */
    public $module;

    final public function up()
    {
        $transaction = $this->db->beginTransaction();
        try {
            if ($this->install() === false) {
                $transaction->rollBack();

                return false;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::warning("Exception: " . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")", __METHOD__);

            $transaction->rollBack();

            return false;
        }

        return null;
    }

    final public function safeDown()
    {
        $transaction = $this->db->beginTransaction();
        try {
            if ($this->uninstall() === false) {
                $transaction->rollBack();

                return false;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::warning("Exception: " . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")", __METHOD__);

            $transaction->rollBack();

            return false;
        }

        return null;
    }

    /**
     * 升级到指定版本
     */
    final public function to($fromVersion, $toVersion)
    {
        $transaction = $this->db->beginTransaction();
        try {
            if ($this->upgrade($fromVersion, $toVersion) === false) {
                $transaction->rollBack();

                return false;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            Yii::warning("Exception: " . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")", __METHOD__);
            $transaction->rollBack();

            return false;
        }

        return null;
    }

    /**
     * 扩展模块被安装时会执行此函数
     * @return mixed
     */
    public function install()
    {
    }

    /**
     * 扩展模块被卸载时会执行此函数
     * @return mixed
     */
    public function uninstall()
    {
    }

    /**
     * 扩展模块被升级时会执行此函数
     * @return mixed
     */
    public function ungrade($fromVersion, $toVersion)
    {
    }

    /**
     * 判断表是否存在(预防表数据安装失败)
     * @param $tableName
     * @return bool
     */
    public function tableExists($tableName)
    {
        return $this->db->schema->getTableSchema($tableName, true) !== null;
    }
}