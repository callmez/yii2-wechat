<?php
/**
 * 该文件是用来生成扩展模块的迁移类
 */

/* @var $this yii\web\View */
/* @var $generator callmez\wechat\generators\module\Generator */

echo "<?php\n";
?>
namespace app\modules\wechat\modules\<?= $generator->moduleID ?>\migrations;

/**
 * 模块数据迁移来.处理模块安装,卸载,升级时的数据操作
 */
class WechatMigration extends AddonModuleMigration
{
    /**
     * 该函数用于模块在安装时执行模块必须数据操作.例如创建数据表,原始数据
     */
//    public function install()
//    {
//    }

    /**
     * 该函数用于模块在卸载时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
//    public function uninstall()
//    {
//    }

    /**
     * 该函数用于模块在升级时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
//    public function ungrade($version)
//    {
//    }
}
