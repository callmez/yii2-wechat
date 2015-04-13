<?php
namespace callmez\wechat\controllers;

use callmez\wechat\components\ProcessController;

/**
 * 微信关注事件默认欢迎类
 * @package callmez\wechat\controllers
 */
class WelcomeController extends ProcessController
{
    /**
     * 默认回复信息
     * @var string
     */
    public $welcomeText;
    public function actionIndex()
    {
        return $this->responseText($this->welcomeText ?: '欢迎关注' . $this->getWechat()->name);
    }
}