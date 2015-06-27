<?php
use yii\helpers\Html;
use callmez\wechat\widgets\GridView;
use callmez\wechat\assets\FileApiAsset;

Yii::$app->request->getIsAjax() && $this->context->layout = false;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="exampleModalLabel">选择媒体素材</h4>
</div>
<div class="modal-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'mediaId',
            'post:ntext',
            'result:ntext',
            'type',
            // 'material',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<div class="modal-footer">
    <?= Html::a('上传文件', ['create'], [
        'class' => 'pull-left btn btn-success',
        'data' => [
            'toggle' => 'modal',
            'target' => '#mediaUploadModal'
        ]
    ]) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
    <button type="button" class="btn btn-primary">确定</button>
</div>