<?php
use \Yii;
use callmez\wechat\modules\admin\widgets\Alert;
use callmez\wechat\modules\admin\widgets\AdminMenu;
use callmez\wechat\modules\admin\assets\AdminAsset;

AdminAsset::register($this);
$this->params['breadcrumbs'] = array_merge([
    [
        'label' => $this->context->module->module->name,
        'url' => ['']
    ]
], isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []);
?>
<?php $this->beginContent($this->context->module->siteAdminLayout) ?>
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-sm-2 mb20">
            <?= AdminMenu::widget([
                'encodeLabels' => false,
                'items' => $this->context->module->getMenus()
            ]) ?>
        </div>
        <div class="col-sm-10"><?= $content ?></div>
    </div>
<?php $this->endContent() ?>