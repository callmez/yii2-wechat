<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use callmez\wechat\models\Wechat;
?>

<div class="wechat-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->inline()->radioList(Wechat::$types) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'app_secret')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'encoding_type')->inline()->radioList(Wechat::$encodings) ?>

    <?= $form->field($model, 'encoding_aes_key')->textInput(['maxlength' => true]) ?>

    <?php if (!$model->getIsNewRecord()): ?>

        <?= $form->field($model, 'hash')->textInput(['disabled' => true]) ?>

        <?= $form->field($model, 'access_token')->textInput(['maxlength' => true]) ?>

    <?php endif ?>

    <?= $form->field($model, 'avatar', ['enableClientValidation' => false])->fileInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qr_code', ['enableClientValidation' => false])->fileInput(['maxlength' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-block btn-success' : 'btn btn-block btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
