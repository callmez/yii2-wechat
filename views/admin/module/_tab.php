<?= \yii\bootstrap\Nav::widget([
    'options' => [
        'class' => 'nav nav-tabs mb20'
    ],
    'items' => [
        [
            'label' => '模块列表',
            'url' => ['admin/module/index']
        ],
        [
            'label' => '卸载模块',
            'url' => ['admin/module/uninstall']
        ],
    ]
])?>

