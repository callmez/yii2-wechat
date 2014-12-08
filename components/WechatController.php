<?php
namespace callmez\wechat\components;

use Yii;

class WechatController extends Controller
{
    /**
     * 是否开启自动获取用户授权获取资料
     * @var bool
     */
    public $enableAuthorizeUserInfo = true;

    public function init()
    {
        parent::init();
        $this->enableAuthorizeUserInfo && $this->wechat->getAuthorizeUserInfo();
    }



}