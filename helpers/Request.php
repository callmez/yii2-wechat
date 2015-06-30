<?php
namespace callmez\wechat\helpers;

use Yii;

/**
 * Request常用设置
 * @package callmez\wechat\helpers
 */
class Request
{
    const QUEYR_PARAM_AJAX = 'ajax';

    /**
     * 是否ajax请求
     * @return bool
     */
    public static function isAjax()
    {
        $request = Yii::$app->request;
        return (bool) $request->get(self::QUEYR_PARAM_AJAX, $request->getIsAjax());
    }
}