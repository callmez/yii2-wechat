<?php

use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;


$this->title = '安装 ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'addon-module-install']]) ?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
<?php PagePanel::end() ?>