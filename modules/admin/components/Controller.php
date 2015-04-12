<?php
namespace callmez\wechat\modules\admin\components;

use Yii;
use yii\filters\AccessControl;
use callmez\wechat\models\Wechat;
use callmez\wechat\components\BaseController;

/**
 * 微信模块管理后台Controller基类
 * @package callmez\wechat\modules\admin\components
 */
class Controller extends BaseController
{
    /**
     * 存储管理微信的session前缀
     */
    const SESSION_WECHAT = 'admin_wechat';
    /**
     * 开启公众号设置验证
     * @var bool
     */
    public $enableWechatRequired = true;
    /**
     * 必须指定先管理公众号才能访问页面
     * @return array
     */
    public function behaviors()
    {
        if ($this->enableWechatRequired) {
            return [
                'access' => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow' => true,
                            'matchCallback' => [$this, 'wechatRequired']
                        ]
                    ]
                ]
            ];
        }
        return [];
    }

    private $_wechat;

    /**
     * 设置当前需要管理的公众号
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        Yii::$app->session->set(self::SESSION_WECHAT, $wechat->id);
        $this->_wechat = $wechat;
    }

    /**
     * 获取当前管理的公众号
     * @return bool|null
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            $id = Yii::$app->session->get(self::SESSION_WECHAT);
            if ($id === null || !($wechat = Wechat::findOne($id))) {
                return false;
            }
            $this->setWechat($wechat);
        }
        return $this->_wechat;
    }

    /**
     * 判断是否需要设置管理公众号
     * @return bool
     */
    public function wechatRequired()
    {
        if ($this->getWechat() || implode('/', [$this->module->module->id, $this->module->id, $this->id]) == 'wechat/admin/wechat') {
            return true;
        }
        $this->flash('未设置管理公众号, 请先选则需要管理的公众号', 'error', ['/wechat/admin/wechat']);
        return false;
    }
}