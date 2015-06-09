<?php
namespace callmez\wechat\helpers;

use Yii;

/**
 * 当前用户类辅助函数
 * @package callmez\wechat\helpers
 */
class User
{

    /**
     * 微信操作权限判断
     * 增加微信管理员ID判断
     * @see yii\wechat\User::can()
     * @return bool
     */
    public static function can($permissionName, $params = [], $allowCaching = true)
    {
        $user = Yii::$app->user;
        $adminId = (array) Yii::$app->getModule('wechat')->adminId;
        return in_array($user->getId(), $adminId) || $user->can($permissionName, $params, $allowCaching);
    }
}