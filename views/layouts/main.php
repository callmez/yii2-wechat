<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use callmez\wechat\widgets\Alert;
use callmez\wechat\assets\WechatAsset;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\widgets\CategoryMenu;

WechatAsset::register($this);

$wechat = $this->context->getWechat(); // 当前设置的管理微信
$currentModule = $this->context->module; // 当前所在模块
$wechatModule = Yii::$app->getModule('wechat'); // 微信主模块
$this->params['breadcrumbs'] = array_merge([
    ['label' => $wechatModule->name, 'url' => ['/wechat']]
], isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [])
?>
<?php $this->beginContent($wechatModule->siteAdminLayout) ?>
    <?= Alert::widget() ?>
    <div class="row">
        <div class="col-sm-2 mb20">
            <?php if ($wechat): ?>
                <?= Html::a($wechat->name, ['/wechat/wechat/update', 'id' => $wechat->id], ['class' => 'btn btn-block btn-success mb10']) ?>
            <?php endif ?>
            <?= CategoryMenu::widget([
                'items' => $wechatModule->getCategoryMenus()
            ]) ?>
        </div>
        <div class="col-sm-10">
            <?php if (ModuleHelper::isAddonModule($currentModule) && ($adminMenus = $currentModule->getAdminMenus()) != []): ?>
                <?php
                NavBar::begin([
                    'brandLabel' => $currentModule->name,
                    'brandUrl' => ModuleHelper::getAdminHomeUrl($currentModule->id),
                    'renderInnerContainer' => false
                ]);
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $currentModule->getAdminMenus(),
                ]);
                NavBar::end();
                ?>
            <?php endif ?>
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent() ?>