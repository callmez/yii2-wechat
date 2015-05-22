<?php
/**
 * 该文件是用来生成扩展模块的迁移类
 */

/* @var $this yii\web\View */
/* @var $generator callmez\wechat\generators\module\Generator */

echo "<?php\n";
?>
namespace <?= $generator->getModuleNamespace() ?>\migrations;

use yii\db\Schema;
use callmez\wechat\components\ModuleMigration;

/**
 * 模块数据迁移来.处理模块安装,卸载,升级时的数据操作
 */
class WechatMigration extends ModuleMigration
{
    /**
     * 该函数用于模块在安装时执行模块必须数据操作.例如创建数据表,原始数据
     */
//    public function install()
//    {
//        // 创建表
//        $tableName = ExampleModel::tableName();
//        if (!$this->tableExists($tableName)) { // 判断表是否已创建
//            $this->createTable($tableName, [
//                'id' => Schema::TYPE_PK,
//                ...
//            ]);
//        }
//
//        // 创建数据
//        $model = new ExampleModel();
//        $model->setAttributes([...]);
//        $model->save();
//    }

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
