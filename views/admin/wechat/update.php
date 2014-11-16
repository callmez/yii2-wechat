<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '公众号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wechat-update">
    <?= Tabs::widget([
        'itemOptions' => [
            'style' => 'padding:25px 0;'
        ],
        'items' => [
            [
                'label' => '普通模式',
                'content' => $this->render('_form', [
                    'model' => $model,
                    'uploader' => $uploader
                ]),
                'active' => !$accountModel->hasErrors()
            ],
            [
                'label' => '自动获取',
                'content' => $this->render('_accountForm', [
                    'model' => $accountModel,
                    'active' => $accountModel->hasErrors()
                ]),
                'active' => $accountModel->hasErrors()
            ]
        ]
    ]) ?>
</div>
