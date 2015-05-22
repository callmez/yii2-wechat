<?php
/* @var $this yii\web\View */
/* @var $generator callmez\wechat\generators\module\Generator */
?>
<?= "<?php " ?>

use callmez\wechat\widgets\PagePanel;

$this->title = '<?= $generator->moduleName ?>';
<?= "?> " ?>

<?= "<?php " ?> PagePanel::begin(['options' => ['class' => '<?= $generator->moduleID . '-admin-index' ?>']]) ?>

    在这里书写您的后台管理视图代码吧!

<?= "<?php " ?> PagePanel::end() ?>