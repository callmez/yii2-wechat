<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use callmez\wechat\sdk\Wechat as WechatSDK;

class Wechat extends WechatSDK
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
     * @see inherit
     */
    public function getAccessToken($force = false)
    {
        if ($force || $this->_accessToken === null || $this->_accessToken['expire'] < YII_BEGIN_TIME) {
            $result = !$force &&
                $this->_accessToken === null &&
                ArrayHelper::getValue($this->model->access_token, 'expire') > YII_BEGIN_TIME
                ? $this->model->access_token : false;
            if ($result === false) {
                if (!($result = $this->requestAccessToken())) {
                    throw new HttpException('Fail to get accessToken from wechat server.');
                }
                $this->trigger(self::EVENT_AFTER_ACCESS_TOKEN_UPDATE, new Event(['data' => $result]));
                $this->model->access_token = $result;
                $this->model->save(false, ['access_token']);
            }
            $this->setAccessToken($result);
        }
        return $this->_accessToken['token'];
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

    /**
     * 查询Wechat model类并返回实例
     * @param array $condition
     * @return bool|object
     */
    public static function instanceByCondition($condition)
    {
        $wechat = \callmez\wechat\models\Wechat::findOne($condition);
        return $wechat ? Yii::createObject([
            'class' => static::className(),
            'model' => $wechat
        ]) : null;
    }

}