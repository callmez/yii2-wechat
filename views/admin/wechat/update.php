<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */

?>
<div class="wechat-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => '普通模式',
                'content' => $this->render('_form', [
                        'model' => $model,
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
