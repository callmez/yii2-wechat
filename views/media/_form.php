<?php

use yii\helpers\Html;
use callmez\wechat\models\Media;
use callmez\wechat\widgets\ActiveForm;
use yii\bootstrap\ButtonGroup;
?>

<div class="media-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-7',
                'error' => 'col-sm-2',
            ],
        ]
    ]); ?>


    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <ul id="mediaTabs" class="list-unstyled btn-group btn-group-justified">
                <?php $mediaType = Yii::$app->request->getQueryParam('mediaType', Media::TYPE_MEDIA) ?>
                <?php array_walk(Media::$mediaTypes, function($type, $key) use ($mediaType) {
                    echo '<li class="btn btn-default' . ($mediaType == $key ? ' active' : '') . '"><div data-toggle="tab" data-target="#' . $key . '">' . $type . '</div></li>';
                }) ?>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div id="media" class="tab-pane <?= ($mediaType == Media::TYPE_MEDIA ? ' active' : '') ?>">
            <?= $form->field($media, 'type')->inline()->radioList(Media::$types) ?>

            <?= $form->field($media, 'material')->inline()->radioList(Media::$materialTypes) ?>

            <?= $form->field($media, 'file')->fileInput() ?>
        </div>

        <div id="news" class="tab-pane <?= ($mediaType == Media::TYPE_NEWS ? ' active' : '') ?>">
            <?= $this->render('_newsForm', [
                'model' => $news
            ]) ?>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax): ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <?= Html::submitButton('提交', ['class' => 'btn btn-block btn-primary']) ?>
            </div>
        </div>
    <?php endif ?>

    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs(<<<EOF
EOF
);