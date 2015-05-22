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
     * @var string
     */
    public $name = '<?= $generator->moduleName ?>';
    /**
     * 扩展模块控制器Namespace
     * @var string
     */
    public $controllerNamespace = '<?= $generator->getModuleNamespace() ?>\controllers';
<?php if ($generator->replyRule): ?>
<?php endif ?>

    /**
     * 返回自定义的模块后台菜单
     * 在这里输出你想要显示的后台菜单列表, 按照yii\widget\Menu需要的数组格式返回既可
     * @return array
     */
//    protected function adminMenus()
//    {
//        return [];
//    }
}
