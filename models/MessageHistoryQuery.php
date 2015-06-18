<?php
namespace callmez\wechat\models;

use yii\db\ActiveQuery;

class MessageHistoryQuery extends ActiveQuery
{
    /**
     * 过滤公众号
     * @param $wid
     * @return $this
     */
    public function wechat($wid)
    {
        return $this->andWhere([
            'wid' => $wid,
        ]);
    }
    /**
     * 查找指定公众号
     * @param $openId
     * @param $original
     * @return $this
     */
    public function wechatFans($original, $openId)
    {
        return $this->orWhere([
            'from' => $openId,
            'to' => $original
        ])->orWhere([
            'to' => $openId,
            'from' => $original
        ]);
    }
}