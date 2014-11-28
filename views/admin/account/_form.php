<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use callmez\storage\widgets\UploadInput;

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
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n</div>\n<div class=\"col-sm-4\">\n{hint}\n{error}\n</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 40]) ?>
    <?= $form->field($model, 'type')->radioList($model::$types) ?>
    <?= $form->field($model, 'token')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'encoding_type')->radioList($model::$encodings) ?>
    <?= $form->field($model, 'encoding_aes_key')->textInput(['maxlength' => 43]) ?>
    <?= $form->field($model, 'app_id')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'app_secret')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'account')->textInput(['maxlength' => 30]) ?>
    <?= $form->field($model, 'original')->textInput(['maxlength' => 40]) ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'description')->textarea(['maxlength' => 255]) ?>
    <?php $upload = [
        'settings' => [
            'autoUpload' => true,
            'onFileComplete' => new \yii\web\JsExpression('onFileComplete')
        ]
    ] ?>
    <?= UploadInput::widget([
        'form' => $form,
        'model' => $model,
        'attribute' => 'avatar',
        'uploader' => $uploader,
        'upload' => $upload
    ]) ?>
    <?= UploadInput::widget([
        'form' => $form,
        'model' => $model,
        'attribute' => 'qr_code',
        'uploader' => $uploader,
        'upload' => $upload
    ]) ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '提交设置' : '提交修改', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs("
function onFileComplete(evt, uiEvt)
{
    if (uiEvt.result.status !== 'success') {
        alert(uiEvt.result.message || '上传失败');
    }
    $(this)
        .find('[type=text]')
        .val(uiEvt.result.message.value)
        .end()
        .find('img')
        .attr('src', uiEvt.result.message.thumbnail + '?' + new Date().getTime());
}
");
