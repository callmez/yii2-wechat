<?php
/* @var $this yii\web\View */
/* @var $generator callmez\wechat\generators\module\Generator */

echo "<?php\n";
?>
namespace <?= $generator->getModuleNamespace() ?>\controllers;

/**
 * 后台默认处理类.
 * 注: 后台类必须继承后台Controller基类以使用微信的管理功能
 */
class AdminController extends \callmez\wechat\components\AdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
