<?php

namespace yii\wechat\controllers;

/**
 * 微信模拟器
 * @package app\controllers
 */
class SimulatorController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
