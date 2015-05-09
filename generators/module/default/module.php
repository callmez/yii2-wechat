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

/**
 * <?= $generator->moduleName ?>
<?= $generator->author ? ' * @author ' . $generator->author : '' ?>
<?= $generator->site ? ' * @link ' . $generator->site : '' ?>
 */
class <?= $className ?> extends \yii\base\Module
{
    /**
     * 扩展模块名称
     */
    public $name = '<?= $generator->moduleName ?>';
    /**
     * 扩展模块控制器Namespace
     */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';
<?php if ($generator->replyRule): ?>
    /**
     * 控制器处理类
     */
    public $controllerMap = [
        // 回复规则控制器
        'reply' => '<?= Yii::$app->getModule('wechat/admin')->controllerNamespace . '\\ReplyController' ?>'
    ];
<?php endif ?>
}
