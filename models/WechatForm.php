<?php
namespace callmez\wechat\models;

use Yii;

class WechatForm extends Wechat
{
    public function rules()
    {
        if ($this->isNewRecord) { // 新创建只需两个填写两个字段,
            return [
                [['name', 'description'], 'required'],
                [['status'], 'defaultStatus']
            ];
        }
        return parent::rules();
    }

    /**
     * 新创建的公众号默认是未激活状态
     * @param $attribute
     * @param $params
     */
    public function defaultStatus($attribute, $params)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_INACTIVE;
        }
    }
}