<?php

use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '微信系统后台管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'default-index']]) ?>
    Hello World!
<?php AdminPanel::end() ?>