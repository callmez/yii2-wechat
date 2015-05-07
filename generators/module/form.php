<?php
?>
<div class="addon-module-form">
    <?= $form->field($generator, 'moduleName') ?>
    <?= $form->field($generator, 'moduleID') ?>
    <?= $form->field($generator, 'moduleClass')->textInput(['maxlength' => true, 'readonly' => true]) ?>
    <?= $form->field($generator, 'version') ?>
    <?= $form->field($generator, 'author')->textInput(['maxlength' => true]) ?>
    <?= $form->field($generator, 'site')->textInput(['maxlength' => true]) ?>
    <?= $form->field($generator, 'migration')->checkbox() ?>
    <?= $form->field($generator, 'adminController')->checkbox() ?>
</div>
<?php
$this->registerJs(<<<EOF
    var form = $('.addon-module-form').closest('form');
    form
        .on('change', '#generator-moduleid', function() {
            form.find('#generator-moduleclass')
                .val('app\\\modules\\\wechat\\\modules\\\' + $(this).val() + '\\\Module');
        });
EOF
);
