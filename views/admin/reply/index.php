<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\models\RuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '回复规则列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rule-index">

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建回复规则', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'layout' => "{summary}\n<div class='table-responsive'>\n{items}\n</div>\n{pager}",
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'options' => [
                    'width' => 30
                ]
            ],
            'name',
            [
                'header' => '关键字',
                'value' => function ($data) {
                    return implode(', ', ArrayHelper::getColumn($data->keywords, 'keyword'));
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return isset($data::$statuses[$data->status]) ? $data::$statuses[$data->status] : '';
                },
                'options' => [
                    'width' => 45
                ]
            ],
            [
                'attribute' => 'priority',
                'options' => [
                    'width' => 50
                ]
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'options' => [
                    'width' => 150
                ]
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
                'options' => [
                    'width' => 150
                ]
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'options' => [
                    'width' => 50
                ]
            ],
        ],
    ]); ?>

</div>
