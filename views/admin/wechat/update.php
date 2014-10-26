<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */

$this->title = 'Update Wechat: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Wechats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wechat-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= Tabs::widget([
        'options' => [
            'style' => 'margin-bottom:20px;'
        ],
        'items' => [
            [
                'label' => '普通模式',
                'content' => $this->render('_form', [
                        'model' => $model,
                    ]),
                'active' => true
            ],
            [
                'label' => '自动获取',
                'content' => $this->render('_loginForm', [
                        'model' => $loginModel,
                    ]),
            ],
        ]
    ]) ?>

</div>
