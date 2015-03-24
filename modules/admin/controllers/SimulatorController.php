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
    public function actionIndex()
    {
        return $this->render('index');
    }

}
