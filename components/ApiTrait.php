<?php
namespace callmez\wechat\components;

use callmez\wechat\controllers\ApiController;
use Yii;

trait ApiTrait
{
    /**
     * 微信服务器请求内容
     * @var array
     */
    public $message;
    /**
     * 微信SDK类
     * @var object
     */
    private $_wechat;

    public function init()
    {
        // 判断请求是ApiController请求而来
        if (Yii::$app->requestedAction->controller instanceof ApiController) {
            $this->message = Yii::$app->requestedAction->controller->message;
            $this->setWechat(Yii::$app->requestedAction->controller->wechat);
        }
        parent::init();
    }

    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * 未经过WechatReceiver类解析的请求或不是来自浏览器的请求都不能访问
     * @return object
     * @throws \yii\web\NotFoundHttpException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->_wechat;
    }
}