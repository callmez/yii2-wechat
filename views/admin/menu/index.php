<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AdminAsset;
AdminAsset::register($this);
$this->title = '自定义菜单管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="menus"></div>
<script id="menuTemplate" type="text/html">
    <div class="btn-group btn-group-justified">
        <% for (var i = 0; i < menus.length; i++) { %>
            <div class="btn-group">
                <button type="button" class="menu btn btn-default"><%= menus[i].name %></button>
                <% if (menus[i].sub_button.length) { %>
                    <div class="sub-menus btn-group-vertical">
                        <% for (var n = 0; n < menus[i].sub_button.length; n++) { %>
                            <button type="button" class="btn btn-default"><%= menus[i].sub_button[n].name %></button>
                        <% } %>
                    </div>
                <% } %>
            </div>
        <% } %>
    </div>
</script>
<?php
$menusJson = json_encode([
    'menus' => $menus
]);
$js = <<<EOF
    var data = {$menusJson};
    var menus = $('#menus').append(template('menuTemplate', data));
EOF;
$this->registerJs($js);