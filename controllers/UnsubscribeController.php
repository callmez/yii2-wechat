<?php
namespace callmez\wechat\controllers;

use callmez\wechat\components\ProcessController;

/**
 * 微信取消关注事件默认处理类
 * @package callmez\wechat\controllers
 */
class UnsubscribeController extends ProcessController
{
    /**
     * 标记取消关注状态
     */
    public function actionIndex()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }
}