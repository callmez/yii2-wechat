<?php

namespace callmez\wechat\models;

use Yii;

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
            'open_id' => '公众号唯一粉丝ID',
            'status' => '关注状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
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
