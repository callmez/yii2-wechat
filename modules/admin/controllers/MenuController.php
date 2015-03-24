<?php
namespace callmez\wechat\modules\admin\controllers;

use Yii;
use callmez\wechat\modules\admin\components\Controller;

class MenuController extends Controller
{
    public function actionIndex()
    {
        $sdk = $this->getWechat()->getSdk();
        // 创建菜单
        if ($menus = Yii::$app->getRequest()->getBodyParam('menus')) {
            if (!$sdk->createMenu($menus)) {
                return $this->message($sdk->getLastErrorInfo('自定义菜单更新失败'));
            }
            return $this->message('自定义菜单更新成功', 'success');
        }
        if (!($menus = $sdk->getMenuList())) {
            return $this->message($sdk->getLastErrorInfo('获取菜单列表失败!'));
        }
        return $this->render('index', [
            'wechat' => $this->getWechat(),
            'menus' => $menus
        ]);
    }
}