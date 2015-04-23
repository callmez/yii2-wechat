<?php
namespace callmez\wechat\modules\admin\controllers;

use Yii;
use callmez\wechat\modules\admin\components\Controller;

/**
 * 微信模拟器
 * @package app\controllers
 */
class SimulatorController extends Controller
{
    public $enableWechatRequired = false;

    public function actionIndex()
    {
        // TODO 按照模拟器的预览效果统一其他代码里的模拟功能
        return $this->render('index');
    }
}
