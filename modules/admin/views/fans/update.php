<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Fans */

$this->title = '修改粉丝';
$this->params['breadcrumbs'][] = ['label' => '粉丝管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>
<div class="fans-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
