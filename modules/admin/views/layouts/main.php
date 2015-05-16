<?php
use \Yii;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use callmez\wechat\modules\admin\widgets\Alert;
use callmez\wechat\models\AdminMenu as AdminMenuModel;
use callmez\wechat\modules\admin\assets\AdminAsset;
use callmez\wechat\modules\admin\widgets\AdminMenu;

AdminAsset::register($this);
$wechat = Yii::$app->getModule('wechat');
$admin = $wechat->getModule('admin');
$module = $this->context->module;
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
                'items' => $admin->getCategoryMenus()
            ]) ?>
        </div>
        <div class="col-sm-10">
            <?php if ($module->hasAdminMenus()): ?>
                <?php
                    NavBar::begin([
                        'brandLabel' => $module->name,
                        'brandUrl' => $module->getAdminHomeUrl(),
                        'renderInnerContainer' => false
                    ]);
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav'],
                        'items' => $admin->getAdminMenus(),
                    ]);
                    NavBar::end();
                ?>
            <?php endif ?>
            <?= $content ?>
        </div>
    </div>
<?php $this->endContent() ?>