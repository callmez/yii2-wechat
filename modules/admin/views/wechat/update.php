<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */

$this->title = '修改公账号: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '微信公众号', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<?php AdminPanel::begin(['options' => ['class' => 'wechat-update']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php AdminPanel::end() ?>