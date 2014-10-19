<?php
namespace yii\wechat\models;

use yii\db\ActiveRecord;

/**
 * 微信公众号
 * @package yii\wechat\models
 */
class Wechat extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%wechat}}';
    }

    public function rules()
    {
        return [
            [['name', 'hash', 'token', 'account', 'orginal', 'app_id', 'app_secret'], 'required'],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    public function beforeSave($insert)
    {
        $return = parent::beforeSave($insert);
        if ($return) {
            $this->access_token = serialize($this->access_token);
        }
        return $return;
    }

    public function afterFind()
    {
        $this->access_token = unserialize($this->access_token);
        return parent::afterFind();
    }
}