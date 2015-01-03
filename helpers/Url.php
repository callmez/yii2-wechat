<?php
namespace callmez\wechat\helpers;

use Yii;
use yii\helpers\ArrayHelper;

class Url extends \yii\helpers\Url
{
    /**
     * 实现路由相对增加删除功能
     * 增加一个参数
     * /index?r=site/index&src=ref1&a=b
     * echo Url::to(['c' => 'd']);
     * get: /index?r=site/index&src=ref1&a=b&c=d
     *
     * 移除一个参数只需把该参数设为null (适用数组参数)
     * /index?r=site/index&src=ref1&a=b&c=d
     * echo Url::to(['c' => null]);
     * get: /index?r=site/index&src=ref1&a=b
     *
     * @param array|string $route
     * @param bool $scheme
     * @return string
     */
    public static function toRoute($route, $scheme = false)
    {
        $route = (array)$route;
        if (isset($route[0])) {
            $route[0] = static::normalizeRoute($route[0]);
        } else {
            $params = Yii::$app->request->queryParams;
            $params[0] = Yii::$app->controller->getRoute();
            $route = ArrayHelper::merge($params, $route);
        }

        if ($scheme) {
            return Yii::$app->getUrlManager()->createAbsoluteUrl($route, is_string($scheme) ? $scheme : null);
        } else {
            return Yii::$app->getUrlManager()->createUrl($route);
        }
    }
}