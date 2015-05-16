<?php
namespace callmez\wechat\modules\text\models;

use yii\db\ActiveRecord;

class ReplyText extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wechat_reply_text}}';
    }

    public function rules()
    {
        return [
            [['rid', 'text'], 'required'],
            [['text'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rid' => '回复规则',
            'text' => '回复内容'
        ];
    }

    /**
     * 获取回复规则
     * @return \yii\db\ActiveQuery
     */
    public function getReplyRule()
    {
        return $this->hasOne(ReplyRule::className(), ['id' => 'rid']);
    }
}