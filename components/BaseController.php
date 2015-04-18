<?php
namespace callmez\wechat\components;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use callmez\wechat\models\Wechat;

/**
 * 微信控制器基类.
 * 所有微信功能类都必须基于此类派生功能
 * @package callmez\wechat\components
 */
abstract class BaseController extends Controller
{
    /**
     * 消息的基本视图
     * @var string
     */
    public $messageLayout = '@callmez/wechat/views/common/message';
    /**
     * 发送消息
     * @param $message
     * @param string $type
     * @param null $redirect
     * @param null $resultType
     * @return array|bool|string
     */
    public function message($message, $type = 'error', $redirect = null, $resultType = null)
    {
        if ($resultType === null) {
            $resultType = Yii::$app->getRequest()->getIsAjax() ? 'json' : 'html';
        } elseif ($resultType === 'flash') {
            $resultType = Yii::$app->getRequest()->getIsAjax() ? 'json' : $resultType;
        }
        $data = [
            'type' => $type,
            'message' => $message,
            'redirect' => Url::to($redirect)
        ];
        switch ($resultType) {
            case 'html':
                return $this->render($this->messageLayout, $data);
            case 'json':
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return $data;
            case 'flash':
                Yii::$app->session->setFlash($type, $message);
                if ($redirect !== null)  {
                    Yii::$app->end(0, $this->redirect($data['redirect']));
                }
                return true;

            default:
                return $message;
        }
    }

    /**
     * flash消息
     * @param $message
     * @param string $status
     * @param null $redirect
     * @return array|bool|string
     */
    public function flash($message, $status = 'error', $redirect = null) {
        return $this->message($message, $status, $redirect, 'flash');
    }

    /**
     * 获取API接口地址
     * @param Wechat $wechat 公众号
     * @param array $params 补充的参数
     * @param bool $scheme 完整地址,或者其他协议完整地址
     * @return string
     */
    public function getApiLink(Wechat $wechat, array $params = [], $scheme = false)
    {
        $token = $wechat->token;
        $nonce = Yii::$app->security->generateRandomString(5);
        $signArray = [$token, TIMESTAMP, $nonce];
        sort($signArray, SORT_STRING);
        $signature = sha1(implode($signArray));
        return Url::to(array_merge([
            '/wechat/api',
            'timestamp' => TIMESTAMP,
            'nonce' => $nonce,
            'signature' => $signature
        ], $params), $scheme);
    }

    /**
     * 主公众号抽象函数
     * @param Wechat $wechat
     * @return mixed
     */
    abstract public function setWechat(Wechat $wechat);

    /**
     * 主公众号抽象, 通过该函数扩展公众号可以管理该公众号的相关功能
     * @return mixed
     */
    abstract public function getWechat();
}