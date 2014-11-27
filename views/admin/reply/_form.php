<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\assets\ArtTemplateAsset;
ArtTemplateAsset::register($this);
?>

<div class="rule-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'labelOptions' => [
                'class' => 'control-label col-sm-2'
            ],
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n</div>\n<div class=\"col-sm-4\">\n{hint}\n{error}\n</div>"
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'status')->dropDownList([
        $model::STATUS_ACTIVE => '启用',
        $model::STATUS_DELETED => '禁用'
    ]) ?>
    <?= $form->field($model, 'priority')->textInput() ?>
    <?php if (!$model->isNewRecord): ?>
        <?= ListView::widget([
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getKeywords()
            ]),
            'itemView' => '_ruleKeyword',
            'viewParams' => [
                'form' => $form
            ]
        ]) ?>
    <?php endif ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button id="addKeyword" class="btn btn-primary" type="button"><span class="glyphicon glyphicon-plus"></span> <b>添加关键字</b></button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <?= Html::submitButton($model->isNewRecord ? '提交设置' : '提交修改', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script id="keywordTemplate" type="text/html">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-10">
            <?= $this->render('_ruleKeyword', [
                'model' => new RuleKeyword,
                'form' => $form
            ]) ?>
        </div>
    </div>
</script>
<?php
$this->registerJs("
    $('#addKeyword').click(function() {
        $(this).closest('.form-group').after(template('keywordTemplate'))
    });
");