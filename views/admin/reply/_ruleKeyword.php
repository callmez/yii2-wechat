<?php
use yii\helpers\Html;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($model->isNewRecord): ?>
            <span class="glyphicon glyphicon-plus"></span> <b>新建关键字</b>
        <?php else: ?>
            <?= Html::encode($model->keyword) ?>
        <?php endif ?>
    </div>
    <div class="panel-body">
        <?= $form->field($model, 'keyword')->textInput(['maxlength' => 40]) ?>
        <?= $form->field($model, 'type')->dropDownList($model::$types) ?>
        <?= $form->field($model, 'priority')->textInput(['maxlength' => 3]) ?>
    </div>
</div>
