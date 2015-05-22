<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\modules\admin\models\ReplyRuleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reply-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wid') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'module') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
