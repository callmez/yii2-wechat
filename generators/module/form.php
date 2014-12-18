<?php
use Yii;
use callmez\wechat\models\Module;
$this->registerCss('
div.required > label:after {
    content: " *";
    color: red;
}
');
?>
<p class="text-warning"><small><code>*</code>号为必填</small></p>
<?php if ($generator->module && Yii::$app->hasModule($generator->module)): ?>
    <p class="alert alert-warning"><?= $generator->module ?>模块已存在, 将创建为<?= $generator->module ?>子模块</p>
<?php endif ?>
<?php
echo $form->field($generator, 'module');
echo $form->field($generator, 'moduleName');
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
echo $form->field($generator, 'services')->checkboxList($generator::$serviceTypes, [
    'itemOptions' => [
        'labelOptions' => [
            'class' => 'checkbox-inline'
        ]
    ]
]);
echo $form->field($generator, 'version');
?>