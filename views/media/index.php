<?php

use yii\helpers\Html;
use callmez\wechat\widgets\GridView;
use callmez\wechat\widgets\PagePanel;

$this->title = '素材管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'media-index']]) ?>
    <p>
        <?= Html::a('添加素材', ['create'], [
            'class' => 'btn btn-success',
            'data' => [
                'toggle' => 'modal',
                'target' => '#mediaCreateModal'
            ]
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'mediaId',
            'post:ntext',
            'result:ntext',
            'type',
             'material',
             'created_at:datetime',
             'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

<?php PagePanel::end() ?>
