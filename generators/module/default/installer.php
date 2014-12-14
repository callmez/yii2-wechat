<?= "<?php\n" ?>
namespace <?= $moduleNamespace ?>;

use callmez\wechat\components\BaseInstaller;

class Installer extends BaseInstaller
{
    public $name = '<?= $generator->moduleName ?>';

    public $description = '<?= $generator->moduleDescription ?>';

    public $type = '<?= $generator->type ?>';

    public $author = '<?= $generator->author ?>';

    public $link = '<?= $generator->link ?>';

    public $version = '<?= $generator->version ?>';

    public function install()
    {
    }

    public function unstall()
    {
    }
}