<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use callmez\wechat\models\MessageHistory;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\modules\admin\models\MessageHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通信记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'message-history-index']]) ?>
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
//            'rid',
//            'kid',
            'open_id',
            'module',
            [
                'attribute' => 'message',
                'value' => function($model) {
                    // TODO 增加详细的信息内容展示, 包括详细内容页
                    switch($type = ArrayHelper::getValue($model->message, 'MsgType')) {
                        case 'text':
                            return $model->message['Content'];
                        default:
                            return $type;
                    }
                },
            ],
            [
                'attribute' => 'type',
                'format' => 'html',
                'value' => function($model) {
                    return  Html::tag('span', MessageHistory::$types[$model->type], [
                        'class' => 'label label-info'
                    ]);
                },
                'filter' => MessageHistory::$types,
                'options' => [
                    'width' => 110
                ]
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
                'template' => '{view} {delete}',
                'options' => [
                    'width' => 55
                ]
            ],
        ],
    ]); ?>
<?php AdminPanel::end() ?>