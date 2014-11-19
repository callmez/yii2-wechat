<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Alert;
use callmez\wechat\helpers\AccountHelper;
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
    <?= Alert::widget([
        'body' => '该功能只能获取部分信息, 部分安全信息请登录微信后台查看并补充.',
        'options' => [
            'class' => 'alert-warning'
        ]
    ]) ?>
    <?= $form->field($model, 'username')
        ->textInput([
        'id' => 'username'
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'imgCode', [
        'options' => [
            'class' => 'hide form-group'
        ],
        'template' => "{label}\n<div class=\"col-sm-2\">{input}</div>\n<div class=\"col-sm-5\"><img id=\"imgVerify\" /> <a id=\"getImgVerify\" href=\"javascript:;\">换一张</a>{hint}\n{error}</div>"
    ]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-10">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs("
var username = $('#username'),
    imgVerify = $('#imgVerify'),
    getImgVerify = $('#getImgVerify'),
    verfyGen = function() {
    var val = username.val();
    if (val) {
        imgVerify.attr('src', '" . AccountHelper::WEIXIN_ROOT . AccountHelper::CAPTCHA_URL . "?username=' + val +'&r='+Math.round(new Date().getTime()));
        imgVerify.closest('.form-group').removeClass('hide');
    }
};
username.blur(verfyGen);
getImgVerify.click(verfyGen);
");
