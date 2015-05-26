<?php

namespace callmez\wechat\models;

use Yii;
use yii\db\ActiveQuery;

class WechatQuery extends ActiveQuery
{
    /**
     * 激活状态
     * @return static
     */
    public function active()
    {
        return $this->andWhere(['status' => Wechat::STATUS_ACTIVE]);
    }
}

