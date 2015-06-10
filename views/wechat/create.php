<?php

use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;

$this->title = '创建公众号';
$this->params['breadcrumbs'][] = ['label' => '公众号列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'wechat-create']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php PagePanel::end() ?>