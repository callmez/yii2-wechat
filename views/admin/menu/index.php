<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);
$this->title = '自定义菜单管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div id="menus" class="col-xs-12">

    </div>
</div>
<?php
$menusJson = json_encode($menus);
$js = <<<EOF
    var menus = {$menusJson};

EOF;
$this->registerJs($js);