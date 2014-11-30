<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\assets\AngularAsset;
AngularAsset::register($this);

$this->title = '自定义菜单管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div ng-app="menuApp">
    <div ng-controller="menusController">
        <button ng-if="menus.length < 3" ng-click="showModal()" id="menuButton" type="button" class="mb20 btn btn-block btn-success">添加一级菜单</button>
        <div class="mb20 btn-group btn-group-justified">
            <div ng-repeat="menu in menus" class="btn-group">
                <a ng-click="showModal($index)" href="javascript:;" class="menu btn btn-lg btn-default">
                    <button ng-click="menus.splice($index, 1)" type="button" class="close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">删除</span>
                    </button>
                    {{menu.name}}
                </a>
            </div>
        </div>
        <div class="btn-group btn-group-justified">
            <div ng-repeat="(index, menu) in menus" class="btn-group">
                <div class="sub-menus btn-group-vertical">
                    <button ng-if="menu.sub_button.length < 5" id="subMenuButton" type="button" class="btn btn-block btn-success">添加二级菜单</button>
                    <a ng-click="showModal(index, $index)" ng-repeat="subMenu in menu.sub_button" href="javascript:;" class="sub-menu btn btn-lg btn-default">
                        <button ng-click="menus[index].sub_button.splice($index, 1)" type="button" class="close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">删除</span>
                        </button>
                        {{subMenu.name}}
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
                        <h4 class="modal-title">修改菜单</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label for="menuName" class="col-sm-2 control-label">菜单名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="menuName" placeholder="请输入菜单名" ng-model="_menu.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="menuType" class="col-sm-2 control-label">菜单类型</label>
                                <div class="col-sm-10">
                                    <select ng-model="_menu.type" class="form-control">
                                        <option value="" ng-selected="!_menu.type">显示子菜单</option>
                                        <option ng-repeat="(key, type) in menuTypes" ng-selected="key == _menu.type" value="{{key}}">{{type.name}}</option>
                                    </select>
                                    <span class="help-block">{{menuTypes[_menu.type].alert}}</span>
                                </div>
                            </div>
                            <div ng-if="_menu.type" ng-hide="menuTypes[_menu.type].value" class="form-group">
                                <label for="menuName" class="col-sm-2 control-label">{{_menu.type == 'click' ? '关键字' : _menu.type == 'view' ? '链接地址' : '类型值'}}</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="menuName" placeholder="请输入菜单名" ng-model="_menu[menuTypes[_menu.type].meta]">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">确认修改</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
var menuApp = angular.module('menuApp', []);
menuApp.controller('menusController', function($scope) {
    $scope.menus = <?= json_encode($menus, JSON_UNESCAPED_UNICODE) ?>;
    $scope.menuTypes = <?= json_encode(\callmez\wechat\models\Wechat::$menuTypes, JSON_UNESCAPED_UNICODE) ?>;
    $scope.showModal = function(index, subMenuIndex) {
        if (typeof subMenuIndex == 'undefined') {
            $scope._menu = $scope.menus[index];
        } else {
            $scope._menu = $scope.menus[index].sub_button[subMenuIndex];
        }

        $('#menuModal').modal();
    }

});
</script>