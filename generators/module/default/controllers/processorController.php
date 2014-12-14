<?= "<?php\n" ?>
namespace <?= $moduleNamespace ?>\controllers;

use callmez\wechat\components\WechatProcessorController;

/**
 * 微信消息处理服务, 用来接受用户的消息服务, 根据业务去求返回微信请求处理结果
 * 类名必须为ProcessorController且继承WechatProcessorController
 */
class ProcessorController extends WechatProcessorController
{
    /**
     * 消息处理默认action, 在此处处理微信消息请求并返回处理结果
     * @return string 返回消息处理后的响应xml
     */
    public function actionIndex()
    {
        return $this->responseText($this->message->content);
    }
}