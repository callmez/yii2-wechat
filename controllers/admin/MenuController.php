<?php
namespace callmez\wechat\controllers\admin;

use callmez\wechat\components\AdminController;

class MenuController extends AdminController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}