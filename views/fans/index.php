<?php

use yii\helpers\Html;
use yii\grid\GridView;
use callmez\wechat\models\Fans;
use callmez\wechat\widgets\PagePanel;
use callmez\wechat\assets\WechatAsset;

$wechatAsset = WechatAsset::register($this);

$this->title = '粉丝列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'fans-update']]) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            [
                'attribute' => 'open_id',
                'options' => [
                    'width' => 200
                ]
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', Fans::$statuses[$model->status], [
                        'class' => 'label label-' . ($model->status == Fans::STATUS_SUBSCRIBED ? 'success' : 'info')
                    ]);
                },
                'filter' => Fans::$statuses,
                'options' => [
                    'width' => 120
                ]
            ],
            [
                'attribute' => 'user.avatar',
                'format' => 'html',
                'value' => function ($model) use ($wechatAsset) {
                    return Html::img($model->user ? $model->user->avatar : $wechatAsset->baseUrl . '/images/anonymous_avatar.jpg', [
                        'width' => 40,
                        'class' => 'img-rounded'
                    ]);
                }
            ],
            [
                'attribute' => 'user.nickname',
                'value' => function ($model) {
                    return $model->user ? $model->user->nickname : '';
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
<?php PagePanel::end() ?>
