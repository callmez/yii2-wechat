<?php

namespace callmez\wechat;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidCallException;
use callmez\wechat\components\WechatReceiver;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'callmez\wechat\controllers';

    /**
     * 微信接收器类设置
     * @var string|array
     */
    public $wechatReceiver = 'callmez\wechat\components\WechatReceiver';
    /**
     * 微信接收器触发的Route ID
     * @var string
     */
    public $wechatReceiverRouterId = 'api';

    /**
     *  增加微信接收器解析
     * @param string $id
     * @return \yii\base\Controller
     */
    public function createControllerByID($id)
    {
        if ($this->wechatReceiverRouterId == $id) {
            $receiver = Yii::createObject($this->wechatReceiver);
            if (!($receiver instanceof WechatReceiver)) {
                throw new InvalidCallException('The wechat receiver class must be instance of "' . WechatReceiver::className() . '" .');
            }
            return Yii::createObject($receiver->resolveController(), [$id, $this]);
        }
        return parent::createControllerByID($id);
    }

    /**
     * 获取微信对接接口地址
     * @param array $params
     * @return string
     */
    public function getWechatReceiverUrl(array $params = array())
    {
        return Url::to(array_merge([
            implode('/', ['', $this->id, $this->wechatReceiverRouterId])
        ], $params), true);
    }
}
