<?php

use yii\helpers\Html;
use yii\bootstrap\ButtonGroup;
use callmez\wechat\models\Media;
use callmez\wechat\widgets\ActiveForm;
use callmez\wechat\widgets\FileApiInputWidget;
?>

<?php $form = ActiveForm::begin([
    'id' => 'mediaForm',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-7',
            'hint' => 'col-sm-2',
        ],
    ],
    'options' => [
        'enctype' => "multipart/form-data"
    ]
]); ?>
    <div class="modal-header">
        <ul class="nav nav-tabs">
            <?php $mediaType = Yii::$app->request->get('mediaType', Media::TYPE_MEDIA) ?>
            <?php array_walk(Media::$mediaTypes, function($type, $key) use ($mediaType) {
                echo '<li class="' . ($mediaType == $key ? ' active' : '') . '"><a href="#' . $key . '" data-toggle="tab" data-value="' . $key . '">' . $type . '</a></li>';
            }) ?>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </ul>
    </div>
    <div class="modal-body">
        <?= Html::hiddenInput('mediaType', $mediaType) ?>

        <div class="tab-content">
            <div id="media" class="tab-pane <?= $mediaType == Media::TYPE_MEDIA ? 'active' : '' ?>">
                <?= $form->field($media, 'type')->inline()->radioList(Media::$types) ?>

                <?= $form->field($media, 'material')->inline()->radioList(Media::$materialTypes) ?>

                <?= $form->field($media, 'file')->fileInput() ?>
            </div>

            <div id="news" class="tab-pane <?= $mediaType == Media::TYPE_NEWS ? 'active' : '' ?>">
                <?= $this->render('_newsForm', [
                    'model' => $news
                ]) ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="js-send btn btn-primary">提交</button>
    </div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<EOF
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var _target = $(e.target);
        var form = _target.closest('form');
        form.find('[name=mediaType]').val(_target.data('value'));
    })
EOF
);