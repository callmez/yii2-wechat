<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use callmez\wechat\models\MessageHistory;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\modules\admin\models\MessageHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通信记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-history-index">

    <<div class="page-header">
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
                'value' => function($model) {
                    return MessageHistory::$types[$model->type];
                },
                'options' => [
                    'width' => 80
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

</div>
