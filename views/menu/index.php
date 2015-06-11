<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use callmez\wechat\models\Wechat;
use callmez\wechat\assets\AngularDragAndDropListsAsset;
use callmez\wechat\widgets\PagePanel;

AngularDragAndDropListsAsset::register($this);

$this->title = '自定义菜单';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'menu-index']]) ?>
<div ng-app="menuApp" class="wechat-menus">
    <div ng-controller="MenusController">
        <div ng-if="menus.length" class="mb10 btn-group btn-group-justified">
            <div ng-repeat="(index, menu) in menus" class="btn-group">
                <p ng-if="menu.sub_button.length > 5" class="text-danger text-center">二级菜单最多不能超过5个</p>
                <ul dnd-list="menu.sub_button" class="sub-menus btn-group-vertical list-unstyled">
                    <li ng-click="showModal(index, $index)" ng-repeat="subMenu in menu.sub_button" dnd-draggable="subMenu" dnd-effect-allowed="move" dnd-moved="menus[index].sub_button.splice($index, 1)" class="sub-menu btn btn-lg btn-default">
                        <button ng-click="menus[index].sub_button.splice($index, 1)" type="button" class="close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">删除</span>
                        </button>
                        {{subMenu.name}}
                    </li>
                    <button ng-if="menu.sub_button.length < 5 && !menu.type" ng-click="showModal(index, 'new')" id="subMenuButton" type="button" class="btn btn-block btn-success">添加二级菜单</button>
                </ul>
            </div>
        </div>
        <div class="mb20">
            <ul dnd-list="menus" class="menus btn-group btn-group-justified list-unstyled mb10">
                <li ng-repeat="menu in menus" dnd-draggable="menu" dnd-effect-allowed="move" dnd-moved="menus.splice($index, 1)" class="menu btn-group">
                    <div ng-click="showModal($index)" class="btn btn-default btn-lg">
                        <button ng-click="menus.splice($index, 1)" type="button" class="close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">删除</span>
                        </button>
                        {{menu.name}}
                    </div>
                </li>
            </ul>
            <p ng-if="menus.length < 3"><button ng-click="showModal('new')" id="menuButton" type="button" class="btn btn-block btn-success">添加一级菜单</button></p>
            <p ng-if="menus.length > 3" class="text-danger text-center">一级菜单最多不能超过3个</p>
        </div>
        <p>
            <button ng-click="resetMenus()" type="button" class="btn btn-info"><span class="glyphicon glyphicon-step-backward"></span> 重置修改 </button>
            <button ng-click="submitMenus()" ng-disabled="submit.disabled" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> {{submit.text || '提交修改'}} </button>
        </p>
        <p class="text-muted"><small><b>Tips</b>: 选中按钮可以拖动来变换顺序哟!</small></p>
        <!-- Modal -->
        <div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">关闭</span>
                        </button>
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
                                        <option ng-repeat="key in notSorted(menuTypes)" ng-init="type = menuTypes[key]" ng-selected="key == _menu.type"
                                                value="{{key}}">{{type.name}}
                                        </option>
                                    </select>
                                    <span class="help-block">{{menuTypes[_menu.type].alert}}</span>
                                </div>
                            </div>
                            <div ng-if="_menu.type" ng-hide="menuTypes[_menu.type].value" class="form-group">
                                <label for="menuName" class="col-sm-2 control-label">{{_menu.type == 'click' ? '关键字' : _menu.type == 'view' ? '链接地址' : '类型值'}}</label>

                                <div class="col-sm-10">
                                    <input ng-model="_menu[menuTypes[_menu.type].meta]" ng-value="{{menuTypes[_menu.type].value}}" type="text" class="form-control" id="menuName">
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
<?php PagePanel::end() ?>
<script type="text/javascript">
    angular.module('menuApp', ['dndLists']).controller('MenusController', function ($scope, $http) {
        $scope._menus = <?= json_encode($menus, JSON_UNESCAPED_UNICODE) ?>;
        $scope.menus = angular.copy($scope._menus);
        $scope.menuTypes = <?= json_encode($this->context->menuTypes, JSON_UNESCAPED_UNICODE) ?>;
        $scope.resetMenus = function() {
            if (confirm('确定要重置所做的修改么?')) {
                $scope.menus = angular.copy($scope._menus);
            }
        }
        $scope.submitMenus = function() {
            // TODO 提交时子菜单类型的一级菜单的二级菜单不能为空.否则提交失败, 需加验证提示
            $scope.submit = {
                disabled: true,
                text: '提交中...'
            };
            var data = {
                menus: $scope.menus
            };
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            $http({
                url: '',
                method: 'POST',
                data: data,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            }).success(function(response) {
                $scope.submit = {
                    disabled: false
                };
                alert(response.message)
            });
        }
        $scope.showModal = function (index, subMenuIndex) {
            if (index == 'new') {
                index = $scope.menus.length;
                $scope.menus[index] = {
                    sub_button: [] // 子菜单需要该属性显示添加菜单按钮
                };
            } else if (subMenuIndex == 'new') {
                subMenuIndex = $scope.menus[index].sub_button.length;
                $scope.menus[index].sub_button[subMenuIndex] = {};
            }
            if (typeof subMenuIndex == 'undefined') {
                $scope._menu = $scope.menus[index];
            } else {
                $scope._menu = $scope.menus[index].sub_button[subMenuIndex];
            }

            $('#menuModal').modal();
        }

        //ngRepeat排序
        $scope.notSorted = function(obj){
            if (!obj) {
                return [];
            }
            return Object.keys(obj);
        }
    });
</script>