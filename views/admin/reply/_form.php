<?php
use \Yii;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use callmez\wechat\components\Receiver;
use callmez\wechat\helpers\ModuleHelper;
?>

<div class="rule-form">
    <?php $form = ActiveForm::begin([
        // 如果提交的关键字中有错误需要现在页面,因为规则已经创建了. 直接提交更新页面
        'action' => $this->context->action->id == 'create' && !$model->getIsNewRecord() ? ['update', 'id' => $model->id] : '',
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'labelOptions' => [
                'class' => 'control-label col-sm-2'
            ],
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n{hint}\n</div>\n<div class=\"col-sm-4\">\n{error}\n</div>"
        ]
    ]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>
    <?= $form->field($model, 'type')->radioList($model::$types, [
        'itemOptions' => [
            'labelOptions' => [
                'class' => 'radio-inline'
            ],
            'data-switch' => 'type',
            'data-closest' => '.form-group'
        ]
    ]) ?>
    <?= $form->field($model, 'reply')->textarea([
        'data-switch-name' => 'type',
        'data-value' => $model::TYPE_REPLY,
    ]) ?>
    <?= $form->field($model, 'processor')->dropDownList($modules, [
        'data-switch-name' => 'type',
        'data-value' => $model::TYPE_PROCCESSOR,
        'maxlength' => 100,
    ]) ?>
    <?= $form->field($model, 'status')->dropDownList($statuses) ?>
    <?= $form->field($model, 'priority')->textInput(['maxlength' => 3]) ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button id="addKeyword" class="btn btn-success" type="button"><span class="glyphicon glyphicon-plus"></span> <b>添加触发关键字</b></button>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= ListView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $keywords
                ]),
                'itemView' => '_ruleKeyword',
                'viewParams' => [
                    'form' => $form
                ],
                'emptyText' => false,
                'summary' => false
            ]) ?>
            <?php if (!$model->getIsNewRecord()): ?>
                <?= ListView::widget([
                    'dataProvider' => new ActiveDataProvider([
                        'query' => $model->getKeywords(),
                        'sort' => [
                            'defaultOrder' => [
                                'created_at' => SORT_DESC
                            ]
                        ]
                    ]),
                    'itemView' => '_ruleKeyword',
                    'viewParams' => [
                        'form' => $form
                    ],
                    'emptyText' => false,
                    'summary' => false
                ]) ?>
            <?php endif ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <?= Html::submitButton($model->getIsNewRecord() ? '提交设置' : '提交修改', ['class' => 'btn btn-block btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script id="keywordTemplate" type="text/html">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-10">
            <?= $this->render('_ruleKeyword', [
                'model' => $ruleKewordModel,
                'form' => $form
            ]) ?>
        </div>
    </div>
</script>
<?php
$keywordsNum = count($keywords);
$script = <<<EOF
    var i = {$keywordsNum};
    $('#addKeyword').click(function() {
        $(this)
            .closest('.form-group')
            .after(template('keywordTemplate')().replace(/(name="[^"\[]+)(\[)([^"]+")/g, '\\$1[new][' + i++ + '][\\$3'));

    });
EOF;
$this->registerJs($script);