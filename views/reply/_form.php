<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use callmez\wechat\models\ReplyRule;
use callmez\wechat\widgets\ActiveForm;
?>

<div class="reply-rule-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal'
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(ReplyRule::$statuses) ?>

    <?= $form->field($model, 'processor')->textInput(['maxlength' => true, 'placeholder' => ReplyRule::PROCESSOR_DEFAULT]) ?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true, 'placeholder' => 0]) ?>

    <div class="form-group">
        <label class="control-label col-sm-3">触发关键字</label>
        <div class="col-sm-6">
            <?php if (!empty($ruleKeywords)): ?>
                <?php foreach ($ruleKeywords as $index => $_ruleKeyword): ?>
                    <?= $this->render('_ruleKeywordForm', [
                        'form' => $form,
                        'index' => $index,
                        'model' => $_ruleKeyword
                    ])?>
                <?php endforeach ?>
            <?php endif ?>
            <button id="addRuleKeyword" class="btn btn-info" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 添加关键字</button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '创建回复规则' : '修改回复规则', [
                'class' => 'btn btn-block ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script id="ruleKeywordTemplate" type="text/html">
    <?= $this->render('_ruleKeywordForm', [
        'form' => $form,
        'model' => $ruleKeyword
    ]) ?>
</script>
<?php
$this->registerJs(<<<EOF
var ruleKeywordNum = 100; // 新建的规则从第100个递增,和已有的规则不冲突(前提是已有的规则不能超过100个)
$(document)
    .on('click', '#addRuleKeyword', function(){
        $(this).before($('#ruleKeywordTemplate').html().replace(/\[\]\[/g, '[' + ruleKeywordNum + ']['));
        ruleKeywordNum++;
    })
    .on('click', '.panel .close', function() {
        if (confirm('确认删除这条关键字么')) {
            $(this).closest('.panel').remove();
        }
    });
EOF
);
