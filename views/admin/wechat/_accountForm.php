<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
            'template' => "{label}\n<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-sm-5\">{hint}\n{error}</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 40]) ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => 5]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-10">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
