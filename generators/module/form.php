<?php
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
echo $form->field($generator, 'identifier');
echo $form->field($generator, 'type')->radioList(Module::$types, [
    'itemOptions' => [
        'labelOptions' => [
            'class' => 'radio-inline'
        ]
    ]
]);
echo $form->field($generator, 'author');
echo $form->field($generator, 'url');
echo $form->field($generator, 'ability');
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