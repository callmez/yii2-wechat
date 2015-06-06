<?php

use yii\helpers\Html;
use callmez\wechat\models\Fans;
use callmez\wechat\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Fans */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fans-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'status')->radioList(Fans::$statuses) ?>

    <?= $form->field($model, 'created_at')->textInput([
        'value' => Yii::$app->formatter->asDatetime($model->created_at),
        'disabled' => true
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('提交', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
