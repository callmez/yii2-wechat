<?php
use yii\helpers\Html;
$prefix = isset($index) ? ($model->getIsNewRecord() ? "[new][{$index}]" : "[{$index}]") : '';
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($model->getIsNewRecord()): ?>
            <span class="glyphicon glyphicon-plus"></span> <b>新建关键字</b>
        <?php else: ?>
            <b><?= Html::encode($model->keyword) ?></b>
        <?php endif ?>
    </div>
    <div class="panel-body">
        <?= $form->field($model, "{$prefix}keyword")->textInput(['maxlength' => 40]) ?>
        <?= $form->field($model, "{$prefix}type")->dropDownList($model::$types) ?>
        <?= $form->field($model, "{$prefix}priority")->textInput(['maxlength' => 3]) ?>
    </div>
</div>
