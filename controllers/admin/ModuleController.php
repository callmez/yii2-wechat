<?php
namespace callmez\wechat\controllers\admin;

use callmez\wechat\components\WechatAdminController;
use callmez\wechat\helpers\ModuleHelper;

class ModuleController extends WechatAdminController
{
    public function actionIndex()
    {
        print_r(ModuleHelper::findWechatModules());
    }
}