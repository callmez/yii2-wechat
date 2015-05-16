<?php
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use callmez\wechat\components\BaseModule;

/**
 * <?= $generator->moduleName ?>

<?php if ($generator->author): ?>
 * @author <?= $generator->author ?>
<?php endif ?>

<?php if ($generator->site): ?>
 * @link <?= $generator->site ?>
<?php endif ?>

 */
class <?= $className ?> extends BaseModule
{
    /**
     * 扩展模块名称
     */
    public $name = '<?= $generator->moduleName ?>';
    /**
     * 扩展模块控制器Namespace
     */
    public $controllerNamespace = '<?= $generator->getModuleBaseNamespace() ?>\controllers';
<?php if ($generator->replyRule): ?>
    /**
     * 控制器处理类
     */
    public $controllerMap = [
        // 回复规则控制器
        'reply' => [
            'class' => '<?= Yii::$app->getModule('wechat/admin')->controllerNamespace ?>\ReplyController'
        ]
    ];
<?php endif ?>
}
