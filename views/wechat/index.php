<?php

use yii\helpers\Html;
use yii\grid\GridView;
use callmez\wechat\models\Wechat;
use callmez\wechat\widgets\PagePanel;

$this->title = '公众号列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'wechat-index']]) ?>
    <p>
        <?= Html::a('添加公众号', ['create'], ['class' => 'btn btn-success']) ?>
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
//            'token',
//            'access_token',
//            'account',
//            'original',
            [
                'attribute' => 'type',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', Wechat::$types[$model->type], [
                        'class' => 'label label-info'
                    ]);
                },
                'filter' => Wechat::$types,

            ],
//            'key',
//            'secret',
//            'encoding_aes_key',
//            'avatar',
//            'qrcode',
//            'address',
//            'description',
//            'username',
//            'status',
//            'password',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', Wechat::$statuses[$model->status], [
                        'class' => 'label label-' . ($model->status == Wechat::STATUS_ACTIVE ? 'success' : 'danger')
                    ]);
                },
                'filter' => Wechat::$statuses,
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'callmez\wechat\widgets\ActionColumn',
                'template' => '{manage} {update} {delete}',
                'buttons' => [
                    'manage' => function ($url, $model) {
                        return Html::a('管理此公众号', $url, [
                            'class' => 'text-danger',
                            'data' => [
                                'toggle' => 'tooltip',
                                'placement' => 'bottom'
                            ],
                            'title' => '管理此公众号'
                        ]);
                    }
                ]
            ]
        ],
    ]); ?>
<?php PagePanel::end() ?>
<?php
$this->registerJs(<<<EOF
    $('[data-toggle="tooltip"]').tooltip();
EOF
);