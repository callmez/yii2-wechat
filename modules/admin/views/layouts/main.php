<?php
use \Yii;
use callmez\wechat\modules\admin\widgets\Alert;
use callmez\wechat\models\AdminMenu as AdminMenuModel;
use callmez\wechat\modules\admin\assets\AdminAsset;
use callmez\wechat\modules\admin\widgets\AdminMenu;
use callmez\wechat\modules\admin\widgets\ModuleMenu;

AdminAsset::register($this);
$wechat = Yii::$app->getModule('wechat');
$admin = $wechat->getModule('admin');
$this->params['breadcrumbs'] = array_merge([
    [
        'label' => $wechat->name,
        'url' => ['']
    ]
], isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []);
?>
<?php $this->beginContent($admin->siteAdminLayout) ?>
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-sm-2 mb20">
            <?= AdminMenu::widget([
                'encodeLabels' => false,
                'items' => $admin->getMenus()
            ]) ?>
        </div>
        <div class="col-sm-10">
            <?= ModuleMenu::widget([
                'encodeLabels' => false,
                'items' => $admin->getModuleMenus()
            ])?>
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent() ?>