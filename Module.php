<?php

namespace callmez\wechat;

use Yii;
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
     *  增加微信接收器控制
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
     * 响应请求
     */
    protected function getWechatControllerID()
    {
        $request = Yii::$app->request;
        switch ($request->method) {
            case 'GET':
                Yii::$app->response->content = $request->getQueryParam('echostr');
                return Yii::$app->end();
            case 'POST':
                $this->wechat = $this->findWechat(); // 查找公众号
                $this->message = $this->parseRequest(); // 解析请求信息
                return $this->matchAction(); // 解析到控制器动作
            default:
                return [];
        }
    }
}
