<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */

$this->title = $model->getIsNewRecord() ? '创建规则' : '修改规则:' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '自动回复管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-update">
    <?= $this->render('_form', [
        'model' => $model,
        'keywords' => $keywords,
        'ruleKewordModel' => $ruleKewordModel
    ]) ?>
</div>
