<?php
namespace callmez\wechat\components;

use yii\db\Migration;

/**
 * 微信扩展模块迁移基类
 * Class AddonModuleMigration
 * @package callmez\wechat\components
 */
class AddonModuleMigration extends Migration
{
    final public function safeUp()
    {
        return $this->install();
    }

    final public function safeDown()
    {
        return $this->uninstall();
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
     * @param $version
     * @return mixed
     */
    public function ungrade($version)
    {
    }
}