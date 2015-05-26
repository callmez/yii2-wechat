<?php
namespace callmez\wechat\components;

use Yii;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Fans;
use callmez\wechat\models\Wechat;

/**
 * 移动页面基类
 * 微信移动页面控制器类必须继承此类
 *
 * @package callmez\wechat\components
 */
class MobileController extends BaseController
{
    /**
     * 存储管理微信的session前缀
     */
    const SESSION_MOBILE_FANS_PREFIX = 'session_mobile_fans';
    /**
     * 微信移动页面模板视图
     * @var string
     */
    public $layout = '@callmez/wechat/views/layouts/mobile';
    /**
     * @var Wechat
     */
    private $_wechat;

    /**
     * 获取公众号
     * 该方法会通过设定的公众wid函数来获取公众号数据
     * @return null
     * @throws NotFoundHttpException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            $wid = Yii::$app->request->getQueryParam(Yii::$app->getModule('wechat')->wechatUrlParam);
            if (!$wid || ($wechat = Wechat::find()->active()->one()) === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $this->setWechat($wechat);
        }
        return $this->_wechat;
    }

    /**
     * 设置公众号
     * @param Wechat $wehchat
     */
    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * @var Fans
     */
    private $_fans;

    /**
     * 获取粉丝数据(当前公众号唯一)
     * @param bool $oauth 是否通过微信服务器Oauth API跳转获取 通过API获取的用户信息会存入session中
     * @return mixed
     */
    public function getFans($oauth = true)
    {
        if ($this->_fans === null) {
            $wechat = $this->getWechat();
            $sessionKey = self::SESSION_MOBILE_FANS_PREFIX . '_' . $wechat->id;
            $openId = Yii::$app->session->get($sessionKey);
            if (!$openId && $oauth) { // API获取用户
                $data = $wechat->getSdk()->getAuthorizeUserInfo('fansInfo');
                if (isset($data['openid'])) {
                    $openId = $data['openid'];
                }
            }
            if (!$openId || ($fans = Fans::findByOpenId($openId)) === null) {
                return false;
            }
            $this->setFans($fans);
        }
        return $this->_fans;
    }

    /**
     * 设置粉丝数据
     * @return mixed
     */
    public function setFans(Fans $fans)
    {
        Yii::$app->session->set(self::SESSION_MOBILE_FANS_PREFIX . '_' . $fans->wid, $fans->open_id);
        return $this->_fans = $fans;
    }
}