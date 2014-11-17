<?php
namespace callmez\wechat\components;

use Yii;
use yii\filters\AccessControl;

class AdminController extends WechatController
{
    const SESSION_MANAGE_WECHAT = 'admin_manage_wechat';
    /**
     * 当前管理的公众号
     * @var object
     */
    private $_manageWechat;

    /**
     * 验证是否设置公众号
     * @return array|void
     */
    public function behaviors()
    {
//        return [];
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [ // 检查是否设置管理公众号.未设置则条状公众号列表界面
                        'allow' => true,
                        'matchCallback' => [$this, 'manageWechatRequired']
                    ],
                ],
            ]
        ];
    }

    /**
     * 设置当前需要管理的公众号
     * @param $id
     */
    public function setManageWechat(Wechat $wechat)
    {
        Yii::$app->session->set(self::SESSION_MANAGE_WECHAT, $wechat->model->id);
        $this->_manageWechat = $wechat;
    }

    /**
     * 获取当前管理的公众号
     * @return bool|object
     */
    public function getManageWechat()
    {
        if ($this->_manageWechat === null) {
            $id = Yii::$app->session->get(self::SESSION_MANAGE_WECHAT);
            if ($id !== null && $wechat = $this->getWechat($id)) {
                return $this->setManageWechat($wechat);
            } else {
                return false;
            }
        }
        return $this->_manageWechat;
    }

    /**
     * 判断是否需要设置管理公众号
     * @return bool
     */
    public function manageWechatRequired()
    {
        if ($this->getManageWechat() || $this->id == 'admin/account') {
            return true;
        }
        if (!Yii::$app->request->getIsAjax()) { // 先设置跳转地址
            Yii::$app->user->setReturnUrl(Yii::$app->request->getUrl());
        }
        Yii::$app->session->setFlash('error', '未设置管理公众号, 请先选则你要管理的公众号');
        Yii::$app->end(0, Yii::$app->getResponse()->redirect(['wechat/admin/account']));
    }
}