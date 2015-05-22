<?php

namespace callmez\wechat\controllers;

use callmez\wechat\components\AdminController;

class DefaultController extends AdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
