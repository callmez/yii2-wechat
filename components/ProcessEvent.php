<?php
namespace callmez\wechat\components;

use yii\base\Event;

/**
 * 微信请求处理事件
 * @package callmez\wechat\components
 */
class ProcessEvent extends Event
{
    /**
     * 微信请求内容信息
     * @var array
     */
    public $message;

    /**
     * 响应公众号Model
     * @var Object
     */
    public $wechat;

    /**
     * 响应内容
     * @var string
     */
    public $result;

    public $isValid = true;

    public function __construct($message, $wechat, $config = [])
    {
        $this->message = $message;
        $this->wechat = $wechat;
        parent::__construct($config);
    }
}