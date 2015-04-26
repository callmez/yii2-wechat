<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\modules\admin\models\FansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '粉丝管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fans-index">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            [
                'attribute' => 'open_id',
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return $model->status;
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'options' => [
                    'width' => 160
                ]
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{history}',
                'buttons' => [
                    'history' => function ($url, $model, $key) {
                         return Html::a('发送消息', ['message', 'id' => $key]);
                    }
                ],
                'options' => [
                    'width' => 80
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
