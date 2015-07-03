<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use callmez\wechat\models\Wechat;
use callmez\wechat\widgets\PagePanel;

$this->title = '公众号列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= GridView::widget([
    'pjax' => true,
    'condensed' => true,
    'responsiveWrap' => false,
    'panel' => [
        'type' => 'default',
        'heading' => '<i class="glyphicon glyphicon glyphicon-list"></i> ' . $this->title,
        'before' => Html::a('添加公众号', ['create'], ['class' => 'btn btn-success', 'data-pjax' => 0]),
    ],

    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => 'kartik\grid\CheckboxColumn',
            'width' => '30px',
        ],

        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'id',
            'width' => '20px',
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'name'
        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'token'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'access_token'
//        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'account'
        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'original'
//        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'type',
            'format' => 'html',
            'filter' => Wechat::$types,
            'value' => function($model) {
                return Html::tag('span', Wechat::$types[$model->type], [
                    'class' => 'label label-info'
                ]);
            }
        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'key'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'secret'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'encoding_aes_key'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'type'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'avatar'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'qrcode'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'address'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'description'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'username'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'status'
//        ],
//        [
//            'class' => 'kartik\grid\DataColumn',
//            'attribute' => 'password'
//        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'status',
            'format' => 'html',
            'filter' => Wechat::$statuses,
            'value' => function($model) {
                return Html::tag('span', Wechat::$statuses[$model->status], [
                    'class' => 'label label-' . ($model->status == Wechat::STATUS_ACTIVE ? 'success' : 'danger')
                ]);
            },
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'created_at',
            'format' => 'datetime'
        ],
        [
            'class' => 'kartik\grid\DataColumn',
            'attribute' => 'updated_at',
            'format' => 'datetime'
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{manage} {update} {delete}',
            'width' => '150px',
            'buttons' => [
                'manage' => function ($url, $model) {
                    return Html::a('管理此公众号', $url, [
                        'class' => 'text-danger',
                        'data-pjax' => 0
                    ]);
                }
            ],
        ]
    ]
]);