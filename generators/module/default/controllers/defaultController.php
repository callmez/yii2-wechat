<?= "<?php\n" ?>
namespace <?= $moduleNamespace ?>\controllers;

use callmez\wechat\components\WechatMobileController;

/**
 * 微信移动web服务, 可根据业务需求实现多个controller服务,如需实现微信服务控制,需继承WechatMobileController
 */
class DefaultController extends WechatMobileController
{

    public function actionIndex()
    {
    }
}