<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wechat-form">
    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'labelOptions' => [
                'class' => 'control-label col-sm-2'
            ],
            'template' => "{label}\n<div class=\"col-sm-5\">{input}</div>\n<div class=\"col-sm-5\">{hint}\n{error}</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 40]) ?>

    <?= $form->field($model, 'hash')->textInput(['maxlength' => 5]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => 32]) ?>

    <?= $form->field($model, 'account')->textInput(['maxlength' => 30]) ?>

    <?= $form->field($model, 'orginal')->textInput(['maxlength' => 40]) ?>

    <?= $form->field($model, 'app_id')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'app_secret')->textInput(['maxlength' => 50]) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
