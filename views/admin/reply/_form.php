<?php
use \Yii;
use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use callmez\wechat\assets\AngularAsset;

AngularAsset::register($this);
?>

<div ng-app="ruleApp" class="rule-form">
    <?php $form = ActiveForm::begin([
        // 如果提交的关键字中有错误需要现在页面,因为规则已经创建了. 直接提交更新页面
        'action' => $this->context->action->id == 'create' && !$model->getIsNewRecord() ? ['update', 'id' => $model->id] : '',
        'options' => [
            'class' => 'form-horizontal',
            'ng-controller' => 'ReplyController'
        ],
        'fieldConfig' => [
            'labelOptions' => [
                'class' => 'control-label col-sm-2'
            ],
            'template' => "{label}\n<div class=\"col-sm-6\">\n{input}\n{hint}\n</div>\n<div class=\"col-sm-4\">\n{error}\n</div>"
        ]
    ]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>

    <?php if ($module): // 模块只能接口回复模式 ?>
        <?= Html::activeHiddenInput($model, 'type', ['value' => $model::TYPE_PROCESSOR]) ?>
        <?= Html::activeHiddenInput($model, 'processor', ['value' => $module]) ?>
    <?php else: // 默认是自动回复模式 ?>
        <?= Html::activeHiddenInput($model, 'type', ['value' => $model::TYPE_REPLY]) ?>
        <?= $form->field($model, 'reply')->textarea() ?>
    <?php endif ?>

    <?= $form->field($model, 'status')->dropDownList($statuses) ?>
    <?= $form->field($model, 'priority')->textInput(['maxlength' => 3]) ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button ng-click="addKeyword()" class="btn btn-success" type="button">
                <span class="glyphicon glyphicon-plus"></span> <b>添加触发关键字</b>
            </button>
        </div>
    </div>
    <div ng-repeat="(key, keyword) in keywords" class="row">
        <div class="col-sm-offset-2 col-sm-10">
            <?= $this->render('_ruleKeyword', [
                'model' => $ruleKewordModel,
                'form' => $form,
                'index' => '{{key}}'
            ]) ?>
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
<script type="text/javascript">
    angular.module('ruleApp', []).controller('ReplyController', function ($scope, $http) {
        $scope.keywords = [];

        $scope.addKeyword = function() {
            $scope.keywords.push([]);
        };
    });
</script>