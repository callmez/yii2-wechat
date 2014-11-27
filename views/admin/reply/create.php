<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */

$this->title = '创建规则';
$this->params['breadcrumbs'][] = ['label' => '自动回复管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
