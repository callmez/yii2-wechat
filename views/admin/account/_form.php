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
                'class' => 'control-label col-sm-3'
            ],
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n{hint}\n</div>\n<div class=\"col-sm-3\">\n{error}\n</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 40]) ?>
    <?= $form->field($model, 'type')->radioList($model::$types) ?>
    <?php if (!$model->getIsNewRecord()): ?>
        <div class="form-group">
            <label class="control-label col-sm-3" for="wechat-api">微信对接接口地址</label>
            <div class="col-sm-6">
                <input type="text" id="wechat-api" class="form-control" value="<?= $this->context->module->getWechatReceiverUrl(['hash' => $model->hash]) ?>" disabled="disabled">
                <div class="help-block">该地址为微信与服务器通信接口地址<br>请复制该地址并填写至微信后台->开发者中心->URL(服务器地址)</div>
            </div>
        </div>
    <?php endif ?>
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
        <div class="col-sm-offset-3 col-sm-6">
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
