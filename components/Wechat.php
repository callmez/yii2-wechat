<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * 微信SDK, 增加微信公众号数据库操作
 * @package callmez\wechat\components
 */
class Wechat extends \callmez\wechat\sdk\Wechat
{
    /**
     * Wechat Model
     * @var object
     */
    public $model;

    /**
     * 根据model自动填充需要的参数
     * @inherit
     */
    public function init() {
        if ($this->model === null) {
            throw new InvalidConfigException('The model property must be set.');
        }
        if ($this->appId === null) {
            $this->appId = $this->model->app_id;
        }
        if ($this->appSecret === null) {
            $this->appSecret = $this->model->app_secret;
        }
        if ($this->token === null) {
            $this->token = $this->model->token;
        }
        parent::init();
    }

    /**
     * 使用数据库来存储access_token
     * @inherit
     */
    protected function requestAccessToken($grantType = 'client_credential')
    {
        if ($result = parent::requestAccessToken($grantType)) {
            $this->model->accessToken = $result;
            $this->model->save(false, ['access_token']);
        }
        return $result;
    }

    /**
     * 跳转微信网页获取用户授权信息
     * @param string $state
     * @return mixed
     */
    public function getAuthorizeUserInfo($state = 'authorize')
    {
        $request = Yii::$app->request;
        if (($code = $request->getQueryParam('code')) && $request->getQueryParam('state') == $state) {
            return $this->getOauth2AccessToken($code);
        } else {
            Yii::$app->getResponse()->redirect($this->getOauth2AuthorizeUrl($request->getAbsoluteUrl(), $state));
            Yii::$app->end();
        }
    }

    /**
     * 返回错误信息(字符串形式)
     * @param string $default
     * @return bool
     */
    public function getLastErrorInfo($default = '未知错误')
    {
        if (isset($this->lastErrorInfo['errcode']) && isset($this->errorCode[$this->lastErrorInfo['errcode']])) {
            return $this->lastErrorInfo['errcode'] . ':' . $this->errorCode[$this->lastErrorInfo['errcode']];
        }
        return is_array($this->lastErrorInfo) ? implode(':', $this->lastErrorInfo) : $this->lastErrorInfo;
    }
}