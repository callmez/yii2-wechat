<?php
use \Yii;
use callmez\wechat\models\Module;
$this->registerCss('
div.required > label:after {
    content: " *";
    color: red;
}
');
?>
<p class="text-warning"><small><code>*</code>号为必填</small></p>
<?php
echo $form->field($generator, 'moduleName');
echo $form->field($generator, 'module');
echo $form->field($generator, 'type')->radioList(Module::$types, [
    'itemOptions' => [
        'labelOptions' => [
            'class' => 'radio-inline'
        ]
    ]
]);
echo $form->field($generator, 'author');
echo $form->field($generator, 'link');
echo $form->field($generator, 'moduleDescription')->textarea();
echo $form->field($generator, 'services')->checkboxList(Module::$serviceTypes, [
    'itemOptions' => [
        'labelOptions' => [
            'class' => 'checkbox-inline'
        ]
    ]
]);
echo $form->field($generator, 'version');
?>
<?php
if ($generator->module && Yii::$app->hasModule($generator->module)) {
    $js = <<<EOF
    $('.default-view-results').prepend('<div class="alert alert-danger">模块{$generator->module}已经存在, 扩展模块创建路径将创建到{$generator->module}/modules目录中</div>');
EOF;
    $this->registerJs($js);
}
?>
