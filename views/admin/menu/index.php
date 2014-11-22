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
    <button id="menuButton" type="button" class="mb20 btn btn-block btn-success">添加一级菜单</button>
    <div class="mb20 btn-group btn-group-justified">
        <% for (var i = 0; i < menus.length; i++) { %>
            <div class="btn-group">
                <a href="javascript:;" data-name="<%= menus[i].name %>" class="menu btn btn-lg btn-default">
                    <button type="button" class="close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">删除</span>
                    </button>
                    <%= menus[i].name %>
                </a>
            </div>
        <% } %>
    </div>
    <div class="btn-group btn-group-justified">
        <% for (var i = 0; i < menus.length; i++) { %>
            <div class="btn-group">
                <div class="sub-menus btn-group-vertical">
                    <% for (var n = 0; n < menus[i].sub_button.length; n++) { %>
                        <a href="javascript:;" data-parent-name="<%= menus[i].name %>" class="sub-menu btn btn-lg btn-default">
                            <button type="button" class="close">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">删除</span>
                            </button>
                            <%= menus[i].sub_button[n].name %>
                        </a>
                    <% } %>
                    <button id="subMenuButton" type="button" class="btn btn-block btn-success">添加二级菜单</button>
                </div>
            </div>
        <% } %>
    </div>
</script>
<?php
$menusJson = json_encode([
    'menus' => $menus
]);
$js = <<<EOF
    var wechatMenu = {
        data: {$menusJson},
        init: function() {
            this.menus = $('#menus').empty().append(template('menuTemplate', this.data));
            this.menuButton = this.menus.find('#menuButton');
            this.subMenuButtons = this.menus.find('[id=subMenuButton]');

            this.initMenuEvent();
            this.checkMenu();
        },
        initMenuEvent: function() {
            var _this = this;
            this.menus.find('.menu, .sub-menu').on('click', function(){

            }).find('.close').on('click', function() {
                var parent = $(this).parent();
                if (parent.hasClass('menu')) { // 删除菜单同样删除子菜单
                    parent.closest('.btn-group').remove();
                    $('[data-parent-name=' + parent.data('name') + ']').closest('.btn-group').remove();
                } else {
                    parent.remove();
                }
                _this.checkMenu();
            });
        },
        checkMenu: function() {
            this.menuButton.toggle(this.menus.find('.menu').length < 3); // 一级菜单 最多3个
            this.subMenuButtons.each(function() {
                var _this = $(this);
                _this.toggle(_this.siblings('.sub-menu').length < 5); // 二级菜单 最多5个
            });
        }
    };
    wechatMenu.init();
EOF;
$this->registerJs($js);