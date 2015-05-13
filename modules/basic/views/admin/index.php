<?php 
use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '基本模块';
?> 
<?php  AdminPanel::begin(['options' => ['class' => 'basic-admin-index']]) ?>

    在这里书写您的后台管理视图代码吧!

<?php  AdminPanel::end() ?>