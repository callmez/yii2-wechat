<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */

$this->title = '修改回复规则: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '回复规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<?php AdminPanel::begin(['options' => ['class' => 'rule-update']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
        'keyword' => $keyword,
        'keywords' => $keywords
    ]) ?>
<?php AdminPanel::end() ?>