<?php
use yii\helpers\Html;
$prefix = isset($index) ? "[{$index}]" : '[]';
?>
<div class="panel <?= $model->getIsNewRecord() ? 'panel-info' : 'panel-default' ?>">
    <div class="panel-heading ">
        <?php if ($model->getIsNewRecord()): ?>
            <span class="glyphicon glyphicon-plus"></span> <b>新建关键字</b>
        <?php else: ?>
            <b><?= Html::encode($model->keyword) ?></b>
        <?php endif ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="panel-body">
        <?= Html::activeHiddenInput($model, "{$prefix}id") ?>
        <?= $form->field($model, "{$prefix}type")->dropDownList($model::$types) ?>
        <?= $form->field($model, "{$prefix}keyword")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}start_at")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}end_at")->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, "{$prefix}priority")->textInput(['maxlength' => true]) ?>
    </div>
</div>
