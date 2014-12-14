<?= "<?php\n" ?>
namespace <?= $moduleNamespace ?>\controllers;

use callmez\wechat\components\WechatReceiverController;

/**
 * 微信订阅类服务, 该服务类似队列服务, 系统会根据特定的订阅队列依次调用, 无返回值.
 * 一般业务场景无此需求, 可不实现此类
 * 注: 如果服务耗时太长会影响微信用户体验. 请慎用!
 */
class ReceiverController extends WechatReceiverController
{
    /**
     *  订阅服务处理默认action, 无返回值.
     */
    public function actionIndex()
    {
        // do something
    }
}