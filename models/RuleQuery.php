<?php
namespace callmez\wechat\models;

use yii\db\ActiveQuery;

class RuleQuery extends ActiveQuery
{
    /**
     * 模块条件检索
     * @param null $module 不为空则查询执行模块规则, 为空查询自动回复规则
     * @return static
     */
    public function filterModule($module = null)
    {
        return $this->andWhere(['module' => $module ?: '']);
    }
}