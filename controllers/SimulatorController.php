<?php
namespace callmez\wechat\controllers;

use Yii;
use callmez\wechat\components\AdminController;

/**
 * 微信模拟器
 * @package app\controllers
 */
class SimulatorController extends AdminController
{
    public $enableWechatRequired = false;

    public function actionIndex()
    {
        // TODO 按照模拟器的预览效果统一其他类型请求的模拟功能
        return $this->render('index');
    }
}
