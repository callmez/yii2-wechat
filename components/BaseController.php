<?php
namespace callmez\wechat\components;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use callmez\wechat\models\Wechat;

/**
 * 微信控制器基类
 * 所有微信模块的控制器必须继承此类
 *
 * @package callmez\wechat\components
 */
abstract class BaseController extends Controller
{
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
        $request = Yii::$app->getRequest();
        if ($resultType === null) {
            $resultType = $request->getIsAjax() ? 'json' : 'html';
        } elseif ($resultType === 'flash') {
            $resultType = Yii::$app->getRequest()->getIsAjax() ? 'json' : $resultType;
        }
        $data = [
            'type' => $type,
            'message' => $message,
            'redirect' => $redirect === null ? null : Url::to($redirect)
        ];
        switch ($resultType) {
            case 'html':
                return $this->render(Yii::$app->getModule('wechat')->messageLayout, $data);
            case 'json':
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return $data;
            case 'flash':
                Yii::$app->session->setFlash($type, $message);
                $data['redirect'] == null && $data['redirect'] = $request->getReferrer();
                Yii::$app->end(0, $this->redirect($data['redirect']));
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
     * 设置当前应用的公众号
     * @param Wechat $wechat
     * @return mixed
     */
    abstract public function setWechat(Wechat $wechat);

    /**
     * 获取当前应用的公众号
     * @return mixed
     */
    abstract public function getWechat();
}