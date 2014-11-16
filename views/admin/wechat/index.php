<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\models\WechatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '公众号管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wechat-index">

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加公众号', ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'hash',
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
