<?php

use yii\helpers\Html;
use yii\grid\GridView;
use callmez\wechat\models\Wechat;

$this->title = '公众号管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wechat-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加公众号', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{manage} {update} {delete}',
                'buttons' => [
                    'manage' => function ($url, $model) {
                        return Html::a('<span class="text-danger glyphicon glyphicon glyphicon-cog"></span>', $url, [
                            'data' => [
                                'toggle' => 'tooltip',
                                'placement' => 'bottom'
                            ],
                            'title' => '管理此公众号'
                        ]);
                    }
                ],
                'options' => [
                    'width' => 70
                ]
            ],
            [
                'attribute' => 'id',
                'options' => [
                    'width' => 30
                ]
            ],
            'name',
//            'hash',
//            'token',
//            'access_token',
//            'account',
//            'original',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return Wechat::$types[$model->type];
                }
            ],
//            'app_id',
//            'app_secret',
//            'encoding_type',
//            'encoding_aes_key',
//            'avatar',
//            'qr_code',
//            'address',
//            'description',
//            'username',
//            'status',
//            'password',
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
            ]
        ],
    ]); ?>
</div>

<?php
$this->registerJs(<<<EOF
    $('[data-toggle="tooltip"]').tooltip();
EOF
);