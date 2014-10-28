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
                'class' => 'control-label col-sm-3'
            ],
            'template' => "{label}\n<div class=\"col-sm-5\">{input}</div>\n<div class=\"col-sm-4\">{hint}\n{error}</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 40]) ?>
    <?= $form->field($model, 'type')->radioList($model::$types) ?>
    <?= $form->field($model, 'token')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'encoding_type')->radioList($model::$encodingTypes) ?>
    <?= $form->field($model, 'encoding_aes_key')->textInput(['maxlength' => 43]) ?>
    <?= $form->field($model, 'app_id')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'app_secret')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'account')->textInput(['maxlength' => 30]) ?>
    <?= $form->field($model, 'orginal')->textInput(['maxlength' => 40]) ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textarea(['maxlength' => 255]) ?>
    <?= $form->field($model, 'avatar')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'qr_code')->textInput(['maxlength' => 255]) ?>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
