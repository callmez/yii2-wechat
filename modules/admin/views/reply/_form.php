<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use callmez\wechat\models\ReplyRule;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Rule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'module')->dropDownList(['' => '选择处理模块'] + ArrayHelper::map(Yii::$app->getModules(true), 'id', 'id')) ?>

    <?= $form->field($model, 'status')->inline()->radioList(ReplyRule::$statuses) ?>

    <?= $form->field($model, 'priority')->textInput() ?>

    <div class="form-group">
        <label class="control-label col-sm-3">触发关键字</label>
        <div class="col-sm-9">
            <?php if (!empty($keywords)): ?>
                <?php foreach ($keywords as $index => $_keyword): ?>
                    <?= $this->render('_keywordForm', [
                        'form' => $form,
                        'index' => $index,
                        'model' => $_keyword
                    ])?>
                <?php endforeach ?>
            <?php endif ?>
            <button id="addKeyword" class="btn btn-info" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加关键字</button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-block btn-success' : 'btn btn-block btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script id="keywordTemplate" type="text/html">
    <?= $this->render('_keywordForm', [
        'form' => $form,
        'model' => $keyword
    ]) ?>
</script>
<?php
$this->registerJs(<<<EOF
var keywordNum = 100;
$(document)
    .on('click', '#addKeyword', function(){
        $(this).before($('#keywordTemplate').html().replace(/\[\]\[/g, '[' + keywordNum + ']['));
        keywordNum++;
    })
    .on('click', '.panel .close', function() {
        if (confirm('确认删除这条关键字么')) {
            $(this).closest('.panel').remove();
        }
    });
EOF
);