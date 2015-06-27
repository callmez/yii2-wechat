<?php
use yii\helpers\Html;
use callmez\wechat\widgets\ActiveForm;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<?php $form = ActiveForm::begin([
    'id' => 'mediaUploadForm',
    'layout' => 'horizontal',
    'options' => [
        'enctype' => 'multipart/form-data'
    ]
]) ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">添加素材</h4>
    </div>
    <div class="modal-body">
        <?= $this->render('_form', [
            'media' => $media,
            'news' => $news
        ]) ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消添加</button>
        <button type="submit" class="js-send btn btn-primary">确定添加</button>
    </div>
<?php ActiveForm::end() ?>
