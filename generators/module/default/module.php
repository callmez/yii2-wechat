<?= "<?php\n" ?>
namespace <?= $moduleNamespace ?>;

use callmez\wechat\components\BaseModule;

class Module extends BaseModule
{
    public $controllerNamespace = '<?= $moduleNamespace ?>\controllers';
}