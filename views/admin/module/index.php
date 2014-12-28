<?php

use yii\helpers\Html;
use yii\grid\GridView;
use callmez\wechat\models\Module;

/* @var $this yii\web\View */
/* @var $searchModel callmez\wechat\models\ModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '扩展模块管理';
$this->params['breadcrumbs'][] = $this->title;
$columns = [
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
        'attribute' => 'module',
        'label' => $model->getAttributeLabel('module'),
        'options' => [
            'width' => 100
        ]
    ],
    [
        'attribute' => 'services',
        'label' => $model->getAttributeLabel('services'),
        'value' => function($data) {
            return implode('<br>', array_map(function($service) {
                return Module::$serviceTypes[$service];
            }, $data['services']));
        },
        'format' => 'html',
        'options' => [
            'width' => 110
        ]
    ],
    [
        'attribute' => 'description',
        'label' => $model->getAttributeLabel('description')
    ]
];
if ($uninstall) {
    $columns = array_merge($columns, [
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'label' => $model->getAttributeLabel('created_at'),
            'options' => [
                'width' => 160
            ]
        ]
    ]);
}
?>
<div class="module-index">
    <?= $this->render('_tab') ?>
    <?= Html::beginForm() ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => $columns,
        ]); ?>
        <button class="btn btn-primary" type="submit" >保存配置</button>
    <?= Html::endForm() ?>
</div>
