<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Wechat */

$this->title = 'Create Wechat';
$this->params['breadcrumbs'][] = ['label' => 'Wechats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wechat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
