<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;


/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */

$this->title = '创建回复规则';
$this->params['breadcrumbs'][] = ['label' => '回复规则列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'rule-create']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
        'keyword' => $keyword
    ]) ?>
<?php AdminPanel::end() ?>
