<?php
namespace callmez\wechat\modules\text\migrations;

use yii\db\Schema;
use callmez\wechat\components\ModuleMigration;
use callmez\wechat\modules\text\models\ReplyText;

/**
 * 模块数据迁移来.处理模块安装,卸载,升级时的数据操作
 */
class WechatMigration extends ModuleMigration
{
    /**
     * 该函数用于模块在安装时执行模块必须数据操作.例如创建数据表,原始数据
     */
    public function install()
    {
        $tableName = ReplyText::tableName();
        if (!$this->tableExists($tableName)) {
            $this->createTable($tableName, [
                'id' => Schema::TYPE_PK,
                'rid' => Schema::TYPE_INTEGER . " UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应的回复规则表ID'",
                'text' => Schema::TYPE_STRING . " NOT NULL DEFAULT '' COMMENT '回复内容'",
            ]);
        }
    }

    /**
     * 该函数用于模块在卸载时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
//    public function uninstall()
//    {
//        // 删除表
//        $this->dropTable(ExampleModel::tableName());
//    }

    /**
     * 该函数用于模块在升级时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
//    public function ungrade($frommVersion, $toVersion)
//    {
//    }
}
