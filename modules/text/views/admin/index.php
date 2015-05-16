<?php
use \Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use callmez\wechat\models\ReplyRule;
use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '文本回复';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'reply-text-index']]) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加文本回复', ['create'], ['class' => 'btn btn-success']) ?>
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
            [
                'attribute' => 'text',
                'label' => '回复内容',
                'value' => function($model) {
                    return $model->replyText ? $model->replyText->text : '';
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'priority',
                'options' => [
                    'width' => 60
                ]
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return Html::tag('span', ReplyRule::$statuses[$model->status], [
                        'class' => 'label label-' . ($model->status == ReplyRule::STATUS_ACTIVE ? 'success' : 'info')
                    ]);
                },
                'filter' => ReplyRule::$statuses,
                'options' => [
                    'width' => 80
                ]
            ],
            [
                'class' => 'callmez\wechat\modules\admin\widgets\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>

<?php AdminPanel::end() ?>