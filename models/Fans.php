<?php

namespace callmez\wechat\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%wechat_fans}}".
 *
 * @property integer $id
 * @property integer $wid
 * @property string $open_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Fans extends \yii\db\ActiveRecord
{
    /**
     * 取消关注
     */
    const STATUS_UNSUBSCRIBED = -1;
    /**
     * 关注状态
     */
    const STATUS_SUBSCRIBED = 0;
    public static $statuses = [
        self::STATUS_SUBSCRIBED => '关注',
        self::STATUS_UNSUBSCRIBED => '取消关注'
    ];

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_fans}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'open_id'], 'required'],
            [['wid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['open_id'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wid' => '所属微信公众号ID',
            'open_id' => '微信ID',
            'status' => '关注状态',
            'created_at' => '关注时间',
            'updated_at' => '修改时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new FansQuery(get_called_class());
    }

    /**
     * 通过唯一的openid查询粉丝
     * @param $open_id
     * @return mixed
     */
    public static function findByOpenId($open_id)
    {
        return self::findOne(['open_id' => $open_id]);
    }

    /**
     * 取消关注状态
     * @return bool
     */
    public function unsubscribe()
    {
        return $this->updateAttributes(['status' => self::STATUS_UNSUBSCRIBED]) > 0;
    }
}
