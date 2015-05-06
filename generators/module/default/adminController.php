<?php
/* @var $this yii\web\View */
/* @var $generator callmez\wechat\generators\module\Generator */

echo "<?php\n";
?>
namespace <?= $generator->getControllerNamespace() ?>;

use callmez\wechat\modules\admin\components\Controller;

/**
 * 后台默认处理类.
 * 注: 后台类必须继承后台Controller基类以使用微信的管理功能
 */
class AdminController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
