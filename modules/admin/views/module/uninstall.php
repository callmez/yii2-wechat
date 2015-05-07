<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\AddonModule */

$this->title = '卸载 ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'addon-module-uninstall']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
<?php AdminPanel::end() ?>