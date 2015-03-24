<?php

namespace callmez\wechat\modules\admin\controllers;

use callmez\wechat\modules\admin\components\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
