<?php
namespace callmez\wechat\components;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;

/**
 * 微信订阅处理控制类, 微信订阅类服务需继承此类 (区别于微信消息类服务, 该服务只接受信息不返回信息)
 * @package callmez\wechat\components
 */
class WechatReceiverController extends WechatController
{
    use ApiTrait
    /**
     * 微信请求关闭csrf验证
     * @var bool
     */
    public $enableCsrfValidation = false;
}