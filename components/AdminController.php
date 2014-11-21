<?php
namespace callmez\wechat\components;

use Yii;
use yii\web\View;
use yii\filters\AccessControl;

class AdminController extends WechatController
{
    const SESSION_MAIN_WECHAT = 'admin_main_wechat';
    /**
     * 当前管理的公众号
     * @var object
     */
    private $_mainWechat;

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
                        'matchCallback' => [$this, 'mainWechatRequired']
                    ],
                ],
            ]
        ];
    }

    /**
     * 设置当前需要管理的公众号
     * @param $id
     */
    public function setMainWechat(Wechat $wechat)
    {
        Yii::$app->session->set(self::SESSION_MAIN_WECHAT, $wechat->model->id);
        $this->_mainWechat = $wechat;
    }

    /**
     * 获取当前管理的公众号
     * @return bool|object
     */
    public function getMainWechat()
    {
        if ($this->_mainWechat === null) {
            $id = Yii::$app->session->get(self::SESSION_MAIN_WECHAT);
            if ($id === null || !($wechat = Wechat::instanceByCondition($id))) {
                return false;
            }
            $this->setMainWechat($wechat);
        }
        return $this->_mainWechat;
    }

    /**
     * 判断是否需要设置管理公众号
     * @return bool
     */
    public function mainWechatRequired()
    {
        if ($this->getMainWechat() || $this->id == 'admin/account') {
            return true;
        }
        if (!Yii::$app->request->getIsAjax()) { // 先设置跳转地址
            Yii::$app->user->setReturnUrl(Yii::$app->request->getUrl());
        }
        Yii::$app->session->setFlash('error', '未设置管理公众号, 请先选则你要管理的公众号');
        Yii::$app->end(0, Yii::$app->getResponse()->redirect(['wechat/admin/account']));
    }

    /**
     * 是否显示微信后台菜单
     * @var bool
     */
    public $menuLayout = '/admin/common/menuLayout';
    public function render($view, $params = [])
    {
        $output = $this->getView()->render($view, $params, $this);
        if ($this->menuLayout !== false) { // 再加一层menulayout
            $output = $this->getView()->render($this->menuLayout, ['content' => $output], $this);
        }
        $layoutFile = $this->findLayoutFile($this->getView());
        if ($layoutFile !== false) {
            return $this->getView()->renderFile($layoutFile, ['content' => $output], $this);
        } else {
            return $output;
        }
    }

    /**
     * 后台显示信息关闭menuLayout
     * @param $message
     * @param string $status
     * @param null $redirect
     * @param null $resultType
     * @return array|string
     */
    public function message($message, $status = 'error', $redirect = null, $resultType = null)
    {
        $this->menuLayout = false;
        return parent::message($message, $status, $redirect, $resultType = null);
    }
}