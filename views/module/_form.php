<?php

use yii\helpers\Html;
use callmez\wechat\widgets\ActiveForm;

$wechat = Yii::$app->getModule('wechat');
?>

<div class="addon-module-form">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'category')->dropDownList($wechat->getCategories(), ['disabled' => true]) ?>

    <?= $form->field($model, 'version')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'ability')->textarea(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'disabled' => true]) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'site')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isInstalled ? '卸载模块' : '安装模块', ['class' => $model->isInstalled ? 'btn btn-block btn-danger' : 'btn btn-block btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
