<?php
namespace callmez\wechat\modules\text\models;

class ReplyRule extends \callmez\wechat\models\ReplyRule
{
    /**
     * 回复文本内容
     * @var string
     */
    public $text;

    /**
     * 禁用事务
     * @return array
     */
    final public function transactions()
    {
        return [];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert || !$this->replyText) { //插入表新建文本内容
            $text = new ReplyText();
            $text->setAttributes([
                'rid' => $this->id,
                'text' => $this->text
            ]);
            $text->save();
        } elseif ($this->replyText && $this->text != $this->getOldAttribute('text')) { // 数据修改更新
            $this->replyText->text = $this->text;
            $this->replyText->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        if ($this->replyText) {
            $this->text = $this->replyText->text;
        }
        parent::afterFind();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['text'], 'required']
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'text' => '回复的文本内容'
        ]);
    }

    public function getReplyText()
    {
        return $this->hasOne(ReplyText::className(), ['rid' => 'id']);
    }
}