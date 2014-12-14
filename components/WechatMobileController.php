<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\InvalidCallException;

/**
 * 微信移动页面控制类, 微信页面类服务需继承此类
 * @package callmez\wechat\components
 */
class WechatMobileController extends WechatController
{
    /**
     * 微信SDK类
     * @var object
     */
    private $_wechat;
    /**
     * 使用微信移动端视图
     * @var string
     */
    public $layout = '@callmez/wechat/views/common/mobileMain';
    /**
     * 是否开启自动获取用户授权获取资料
     * @var bool
     */
    public $enableAuthorizeUserInfo = true;

    /**
     * 微信公众号ID Url参数名
     * @var string
     */
    public $wechatQueryParamName = 'wid';

    public function init()
    {
        parent::init();
//        if ($this->enableAuthorizeUserInfo) { //
//            $this->getAuthorizeUserInfo();
//        }
    }

    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * 根据指定wid参数查找相应的公众号
     * @return null|object
     * @throws \yii\base\InvalidCallException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            $wid = Yii::$app->request->getQueryParam($this->wechatQueryParamName);
            if ($wid && $wechat = Wechat::instanceByCondition(['id' => $wid])) {
                $this->setWechat($wechat);
            } else {
                throw new InvalidCallException("Can't find wechat.");
            }
        }
        return $this->_wechat;
    }

    /**
     * 获取授权用户信息
     */
    public function getAuthorizeUserInfo()
    {
        $this->getWechat()->getAuthorizeUserInfo();
    }
}