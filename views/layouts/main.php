<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use callmez\wechat\widgets\Alert;
use callmez\wechat\assets\WechatAsset;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\widgets\CategoryMenu;

WechatAsset::register($this);

$wechat = Yii::$app->getModule('wechat');
$module = $this->context->module;
$this->params['breadcrumbs'] = array_merge([
    ['label' => $wechat->name, 'url' => ['/wechat']]
], isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [])
?>
<?php $this->beginContent($wechat->siteAdminLayout) ?>
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-sm-2 mb20">
            <?= CategoryMenu::widget([
                'items' => $wechat->getCategoryMenus()
            ]) ?>
        </div>
        <div class="col-sm-10">
            <?php if (ModuleHelper::isAddonModule($module) && ($adminMenus = $module->getAdminMenus()) != []): ?>
                <?php
                NavBar::begin([
                    'brandLabel' => $module->name,
                    'brandUrl' => ModuleHelper::getAdminHomeUrl($module->id),
                    'renderInnerContainer' => false
                ]);
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $module->getAdminMenus(),
                ]);
                NavBar::end();
                ?>
            <?php endif ?>
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent() ?>