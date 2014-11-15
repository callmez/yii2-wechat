<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */

$this->title = 'Create Wechat';
$this->params['breadcrumbs'][] = ['label' => 'Wechats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wechat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Tabs::widget([
        'items' => [
            [
                'label' => '普通模式',
                'content' => $this->render('_form', [
                        'model' => $model,
                        'uploader' => $uploader
                    ]),
                'active' => true
            ],
            [
                'class' => 'nav-pane fade',
                'label' => '自动获取',
                'content' => $this->render('_accountForm', [
                        'model' => $accountModel,
                    ]),
            ]
        ]
    ]) ?>

</div>
