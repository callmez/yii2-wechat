<?php

use yii\helpers\Html;
use callmez\wechat\models\Rule;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\modules\admin\models\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '回复规则列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-index">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?//= $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建回复规则', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => [
                    'width' => 75
                ]
            ],
            'name',
            'module',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Rule::$statuses[$model->status];
                },
                'options' => [
                    'width' => 80
                ]
            ],
            // 'priority',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'options' => [
                    'width' => 160
                ]
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'options' => [
                    'width' => 160
                ]
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => [
                    'width' => 55
                ]
            ],
        ],
    ]); ?>

</div>
