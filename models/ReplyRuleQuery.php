<?php
namespace callmez\wechat\models;

use yii\db\ActiveQuery;

class ReplyRuleQuery extends ActiveQuery
{

    /**
     * 查询状态
     * @param int $status 启用
     * @return $this
     */
    public function active($status = ReplyRule::STATUS_ACTIVE)
    {
        return $this->andWhere(['status' => $status]);
    }
}