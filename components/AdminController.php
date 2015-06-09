<?php
namespace callmez\wechat\components;

use Yii;
use yii\filters\AccessControl;
use callmez\wechat\helpers\User;
use callmez\wechat\models\Wechat;

/**
 * 微信管理后台控制器基类
 * 后台管理类虚继承此类
 *
 * @package callmez\wechat\components
 */
class AdminController extends BaseController
{
    /**
     * 存储管理微信的session key
     */
    const SESSION_MANAGE_WECHAT_KEY = 'session_manage_wechat';
    /**
     * 默认后台主视图
     * @var string
     */
    public $layout = '@callmez/wechat/views/layouts/main';
    /**
     * 开启设置公众号验证
     * @var bool
     */
    public $enableCheckWechat = true;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => array_merge([
                    [
                        'allow' => true,
                        'roles' => ['@'], // 登录才能操作后台
                        'matchCallback' => function() {
                            // 是否设置应用公众号
                            if ($this->enableCheckWechat && !$this->getWechat()) {
                                $this->flash('未设置管理公众号, 请先选则需要管理的公众号', 'error', ['/wechat/wechat']);
                                return false;
                            }
                            return User::can('manage-wechat');
                        }
                    ]
                ], Yii::$app->getModule('wechat')->adminAccessRule ?: []) // 自定义验证
            ]
        ];
    }

    /**
     * @var Wechat
     */
    private $_wechat;

    /**
     * 设置当前需要管理的公众号
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        Yii::$app->session->set(self::SESSION_MANAGE_WECHAT_KEY, $wechat->id);
        $this->_wechat = $wechat;
    }

    /**
     * 获取当前管理的公众号
     * @return Wechat|null
     * @throws InvalidConfigException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            $wid = Yii::$app->session->get(self::SESSION_MANAGE_WECHAT_KEY);
            if (!$wid || ($wechat = Wechat::findOne($wid)) === null) {
                return false;
            }
            $this->setWechat($wechat);
        }
        return $this->_wechat;
    }
}