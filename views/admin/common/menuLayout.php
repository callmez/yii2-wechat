<?php
use yii\bootstrap\Nav;
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
        'label' => '<span class="glyphicon glyphicon-cog"></span> ' . '公众号管理',
        'url' => ['admin/account/index']
    ],
    [
        'label' => '<span class="glyphicon glyphicon-list"></span> ' . '自定义菜单管理',
        'url' => ['admin/menu/index']
    ],
    [
        'label' => '<span class="glyphicon glyphicon-list"></span> ' . '自动回复管理',
        'url' => ['admin/reply/index']
    ]
];
if ($wechat = $this->context->getMainWechat()) {
    $items = array_merge([
        [
            'label' => '<span class="glyphicon glyphicon-user"></span> ' . $wechat->model->name,
            'url' => ['admin/account/view']
        ]
    ], $items);
}
?>
<div class="row">
    <div class="col-sm-2 mb20">
        <?= Nav::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked'
            ],
            'encodeLabels' => false,
            'items' => $items
        ]) ?>
    </div>
    <div class="col-sm-10"><?= $content ?></div>
</div>