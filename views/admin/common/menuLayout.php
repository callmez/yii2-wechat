<?php
use yii\bootstrap\Nav;
$this->params['breadcrumbs'] = array_merge([
    [
        'label' => '微信',
        'url' => ['admin/account']
    ]
], $this->params['breadcrumbs']);
?>
<div class="row">
    <div class="col-sm-2">

        <?= Nav::widget([
            'options' => [
                'class' => 'nav nav-pills nav-stacked'
            ],
            'items' => [
                [
                    'label' => '公众号管理',
                    'url' => ['admin/account/index']
                ],
                [
                    'label' => '自定义菜单管理',
                    'url' => ['admin/menu/index']
                ]
            ]
        ]) ?>
    </div>
    <div class="col-sm-10"><?= $content ?></div>
</div>