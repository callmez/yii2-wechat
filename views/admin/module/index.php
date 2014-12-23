<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\models\ModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '扩展模块管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index">
    <?= $this->render('_tab') ?>
    <?= Html::beginForm() ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckBoxColumn',
                    'checkboxOptions' => function($model, $key, $index, $view) use ($installedModules, $uninstall) {
                        $options = [
                            'value' => $key
                        ];
                        if (!$uninstall && array_key_exists($key, $installedModules)) {
                            $options['checked'] = true;
                            $options['disabled'] = true;
                        }
                        return $options;
                    },
                    'options' => [
                        'width' => 30
                    ]
                ],

                [
                    'attribute' => 'name',
                    'label' => $model->getAttributeLabel('name'),
                    'options' => [
                        'width' => 200
                    ]
                ],
                [
                    'attribute' => 'description',
                    'label' => $model->getAttributeLabel('description')
                ]
            ],
        ]); ?>
        <button class="btn btn-primary" type="submit" >保存配置</button>
    <?= Html::endForm() ?>
</div>
