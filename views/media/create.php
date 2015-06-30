<?php
use yii\helpers\Html;
use callmez\wechat\widgets\ActiveForm;

$this->title = '添加媒体素材';
Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
    <?= $this->render('_form', [
        'media' => $media,
        'news' => $news
    ]) ?>
