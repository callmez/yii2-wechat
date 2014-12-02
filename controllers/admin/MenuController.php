<?php
namespace callmez\wechat\controllers\admin;

use Yii;
use callmez\wechat\components\AdminController;

class MenuController extends AdminController
{
    public function actionIndex()
    {
        $wechat = $this->getMainWechat();
        // 创建菜单
        if ($menus = Yii::$app->getRequest()->getBodyParam('menus')) {
            if (!$wechat->createMenu($menus)) {
                return $this->message($wechat->lastErrorInfo['errmsg'] ?: '自定菜单更新失败');
            }
            return $this->message('自定义菜单更新成功', 'success');
        }
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