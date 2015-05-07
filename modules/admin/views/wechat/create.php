<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '创建公众号';
$this->params['breadcrumbs'][] = ['label' => '微信公众号', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'wechat-create']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php AdminPanel::end() ?>