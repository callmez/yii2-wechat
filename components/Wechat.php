<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\InvalidConfigException;

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
        !empty($this->model->access_token) && $this->setAccessToken($this->model->access_token);
        parent::init();
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
        ]) : false;
    }

}