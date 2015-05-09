<?php
use \Yii;
use callmez\wechat\modules\admin\widgets\Alert;
use callmez\wechat\models\AddonModule;
use callmez\wechat\models\AdminMenu as AdminMenuModel;
use callmez\wechat\modules\admin\widgets\AdminMenu;
use callmez\wechat\modules\admin\assets\AdminAsset;

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
                <?php if ($this->beginCache('adminMenus', [ // 缓存菜单
                    'dependency' => [
                        'class' => 'yii\caching\TagDependency',
                        'tags' => [AddonModule::CACHE_DATA_DEPENDENCY_TAG, AdminMenuModel::CACHE_DATA_DEPENDENCY_TAG] // 后台菜单和扩展模块数据变更后更新缓存
                    ]
                ])): ?>
                    <?= AdminMenu::widget([
                        'encodeLabels' => false,
                        'items' => $admin->getMenus()
                    ]) ?>
                    <?php $this->endCache() ?>
                <?php endif ?>
            </div>
            <div class="col-sm-10"><?= $content ?></div>
        </div>
<?php $this->endContent() ?>