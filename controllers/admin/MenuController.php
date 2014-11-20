<?php
namespace callmez\wechat\controllers\admin;

use callmez\wechat\components\AdminController;

class MenuController extends AdminController
{
    public function actionIndex()
    {
        $wechat = $this->getMainWechat();
        $menus = $wechat->getMenuList();
        if (!$menus) {
            return $this->message($wechat->getLastErrorInfo('获取菜单列表失败!'));
        }
        return $this->render('index', [
            'wechat' => $wechat,
            'menus' => $menus
        ]);
    }

}