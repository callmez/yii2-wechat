<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Fans */

$this->title = '修改粉丝';
$this->params['breadcrumbs'][] = ['label' => '粉丝管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>
<div class="fans-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
