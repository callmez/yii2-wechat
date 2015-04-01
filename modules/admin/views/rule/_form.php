<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use callmez\wechat\models\Rule;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'module')->dropDownList(array_map(function ($module) {
        return $module->id;
    }, Yii::$app->getModules(true))) ?>

    <?= $form->field($model, 'status')->inline()->radioList(Rule::$statuses) ?>

    <?= $form->field($model, 'priority')->textInput() ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-block btn-success' : 'btn btn-block btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
