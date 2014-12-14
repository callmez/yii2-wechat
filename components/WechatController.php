<?php
namespace callmez\wechat\components;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use \yii\web\Controller;
use yii\base\InvalidConfigException;

/**
 * 微信控制器基类
 * @package callmez\wechat\components
 */
abstract class WechatController extends Controller
{
    abstract public function setWechat(Wechat $wechat);
    abstract public function getWechat();

    /**
     * 信息渲染视图s
     * @var string
     */
    public $messageLayout = '/common/message';
    /**
     * @param $message 信息显示内容
     * @param string $status 信息显示状态, ['info', 'success', 'error', 'warning']
     * @param null $redirect 跳转地址
     * @param null $resultType 信息显示格式
     * @return array|string
     */
    public function message($message, $status = 'error', $redirect = null, $resultType = null)
    {
        $resultType === null && $resultType = Yii::$app->getRequest()->getIsAjax() ? 'json' : 'html';
        $redirect = Url::to($redirect);
        $data = [
            'status' => $status,
            'message' => $message,
            'redirect' => $redirect
        ];

        if ($resultType === 'json') {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            return $data;
        } elseif ($resultType === 'html') {
            return $this->render($this->messageLayout, $data);
        } else {
            return $message;
        }
    }
}