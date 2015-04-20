<?php

namespace callmez\wechat\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * 微信通信历史记录
 *
 * @property integer $id
 * @property integer $wid
 * @property integer $rid
 * @property integer $kid
 * @property string $open_id
 * @property string $module
 * @property string $message
 * @property string $type
 * @property integer $created_at
 */
class MessageHistory extends \yii\db\ActiveRecord
{
    /**
     * 微信请求信息
     */
    const TYPE_REQUEST = 'request';
    /**
     * 微信请求后的系统响应信息
     */
    const TYPE_RESPONSE = 'response';
    /**
     * 主动客服消息
     */
    const TYPE_CUSTOMER = 'customer';
    public static $types = [
        self::TYPE_REQUEST => '微信请求',
        self::TYPE_RESPONSE => '系统响应',
        self::TYPE_CUSTOMER => '客服消息'
    ];
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ]
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_message_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wid', 'module', 'message', 'open_id', 'type'], 'required'],
            [['wid', 'rid', 'kid', 'created_at'], 'integer'],
            [['open_id'], 'string', 'max' => 50],
            [['module'], 'string', 'max' => 20],
            [['type'], 'string', 'max' => 10]
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
            'rid' => '所属规则ID',
            'kid' => '所属关键字ID',
            'open_id' => '请求用户微信ID',
            'module' => '处理模块',
            'message' => '消息体内容',
            'type' => '发送类型',
            'created_at' => '关注时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (is_array($this->message)) {
            $this->message = serialize($this->message);
        }
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $this->message = unserialize($this->message);
        parent::afterFind();
    }
}
