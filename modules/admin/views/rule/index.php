<?php

use yii\helpers\Html;
use callmez\wechat\models\ReplyRule;
use yii\grid\GridView;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\modules\admin\models\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '回复规则列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'rule-index']]) ?>
    <?//= $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建回复规则', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover'],
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
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', ReplyRule::$statuses[$model->status], [
                        'class' => 'label label-' . ($model->status == ReplyRule::STATUS_ACTIVE ? 'success' : 'info')
                    ]);
                },
                'filter' => ReplyRule::$statuses,
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
<?php AdminPanel::end() ?>
