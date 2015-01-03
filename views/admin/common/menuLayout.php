<?php
use \Yii;
use yii\bootstrap\Nav;
use callmez\wechat\widgets\Alert;

use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);
$this->params['breadcrumbs'] = array_merge([
    [
        'label' => '微信',
        'url' => ['admin/account']
    ]
], isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []);

$items = [
    [
        'label' => '<span class="glyphicon glyphicon-cog"></span> 公众号管理',
        'url' => ['admin/account'],
        'active' => $this->context->id == 'admin/account' && $this->context->action->id != 'view'
    ],
    [
        'label' => '<span class="glyphicon glyphicon-plus"></span> 扩展模块管理',
        'url' => ['admin/module'],
        'active' => $this->context->id == 'admin/module'
    ],
    [
        'label' => '<span class="glyphicon glyphicon-list"></span> 自定义菜单管理',
        'url' => ['admin/menu'],
        'active' => $this->context->id == 'admin/menu'
    ],
    [
        'label' => '<span class="glyphicon glyphicon-envelope"></span> 自动回复',
        'url' => ['admin/reply'],
        'active' => $this->context->id == 'admin/reply'
    ],
    [
        'label' => '<span class="glyphicon glyphicon-send"></span> 微信模拟器',
        'url' => ['admin/simulator'],
        'active' => $this->context->id == 'admin/simulator'
    ]
];
if ($wechat = $this->context->getWechat()) {
    $items = array_merge([
        [
            'label' => '<span class="glyphicon glyphicon-user"></span> ' . $wechat->model->name,
            'url' => ['admin/account/view']
        ]
    ], $items);
}

?>
<?= Alert::widget() ?>
<div class="row">
    <div class="col-sm-2 mb20">
        <?= Nav::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked'
            ],
            'encodeLabels' => false,
            'items' => $items
        ]) ?>
        <?php if (($installedModules = $this->context->module->getInstalledModules()) !== []): ?>
            <hr>
            <?= Nav::widget([
                'options' => [
                    'class' => 'nav nav-pills nav-stacked'
                ],
                'items' => array_map(function ($module){
                    return [
                        'label' => $module->name
                    ];
                }, $installedModules)
            ]) ?>
        <?php endif ?>
    </div>
    <div class="col-sm-10"><?= $content ?></div>
</div>