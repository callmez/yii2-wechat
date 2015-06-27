<?php
use yii\helpers\Html;
use callmez\wechat\models\Message;
use callmez\wechat\widgets\ActiveForm;
use callmez\wechat\assets\MessageAsset;
use callmez\wechat\widgets\FileApiInputWidget;

MessageAsset::register($this);
?>
<?php $form = ActiveForm::begin([
    'id' => 'messageForm',
    'layout' => 'horizontal'
]); ?>
    <?= Html::activeHiddenInput($model, 'toUser') ?>

    <?= $form->field($model, 'msgType')->inline()->radioList(Message::$types) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'musicUrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hqMusicUrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea() ?>

    <?= $form->field($model, 'description')->textarea() ?>

    <?php $a = Html::a('选择文件', ['media/pick'], [
        'class' => 'btn btn-default',
        'data' => [
            'toggle' => 'modal',
            'target' => '#mediaModal'
        ]
    ]) ?>
    <?php $a = Html::a('浏览素材', ['media/pick'], [
        'class' => 'btn btn-default',
        'data' => [
            'toggle' => 'modal',
            'target' => '#mediaModal'
        ]
    ]) ?>

    <?= $form->field($model, 'mediaId', [
        'inputTemplate' => '<div class="input-group"><span class="input-group-btn">' . $a . '</span>{input}</div>',
    ])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mediaId')->widget(FileApiInputWidget::className(), [
        'template' => "\n<div id=\"{id}\" class=\"input-group\">\n<div class=\"input-group-btn\">\n{button}{fields}\n</div>\n{input}\n<div class=\"input-group-btn\">\n{$a}\n</div></div>\n",
        'fields' => Html::hiddenInput('mediaType'),
        'jsOptions' => [
            'url' => $uploadUrl
        ]
    ]) ?>
    <?= $form->field($model, 'mediaId')->widget(FileApiInputWidget::className(), [
        'fields' => Html::hiddenInput('mediaType'),
        'jsOptions' => [
            'url' => $uploadUrl
        ]
    ]) ?>

    <?= $form->field($model, 'thumbMediaId')->widget(FileApiInputWidget::className(), [
        'jsOptions' => [
            'url' => $uploadUrl
        ]
    ]) ?>

    <?= $form->field($model, 'musicUrl')->widget(FileApiInputWidget::className(), [
        'jsOptions' => [
            'url' => $uploadUrl
        ]
    ]) ?>

    <div class="form-group submit-button">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton('发送', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<EOF
    $('#messageForm').message();
EOF
);