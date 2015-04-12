<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */

$this->title = '创建回复规则';
$this->params['breadcrumbs'][] = ['label' => '回复规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'keyword' => $keyword
    ]) ?>

</div>
