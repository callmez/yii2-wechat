<?php

use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;

$this->title = '修改公账号: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '微信公众号', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<?php PagePanel::begin(['options' => ['class' => 'wechat-update']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php PagePanel::end() ?>