<?php
use yii\helpers\Html;
use callmez\wechat\models\Module;
?>
<div class="addon-module-form">
    <?= Html::activeHiddenInput($generator, 'type') ?>
    <?= $form->field($generator, 'moduleName') ?>
    <?= $form->field($generator, 'moduleID') ?>
    <?= $form->field($generator, 'moduleClass')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($generator, 'version') ?>
    <?= $form->field($generator, 'author')->textInput(['maxlength' => true]) ?>
    <?= $form->field($generator, 'site')->textInput(['maxlength' => true]) ?>
    <?= $form->field($generator, 'migration')->checkbox() ?>
    <?= $form->field($generator, 'admin')->checkbox() ?>
    <?= $form->field($generator, 'replyRule')->checkbox() ?>
    <?= $form->field($generator, 'category')->dropDownList(array_merge(['' => '请选择分类'], $generator->getCategories())) ?>
</div>
<?php
$namespaces = json_encode([
    Module::TYPE_ADDON => $generator->getBaseNamespace(Module::TYPE_ADDON),
    Module::TYPE_CORE => $generator->getBaseNamespace(Module::TYPE_CORE),
]);
$this->registerJs(<<<EOF
    var form = $('.addon-module-form').closest('form');
    var namespaces = $namespaces;
    form
        .on('change', '#generator-moduleid', function() {
            form.find('#generator-moduleclass')
                .val(namespaces[form.find('#generator-type').val()] + '\\\' + $(this).val() + '\\\Module');
        });
EOF
);
