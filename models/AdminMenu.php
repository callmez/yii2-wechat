<?php
namespace callmez\wechat\models;

use yii\db\ActiveRecord;

class AdminMenu extends ActiveRecord
{
    /**
     * 后台菜单数据缓存依赖TAG
     */
    const CACHE_DATA_DEPENDENCY_TAG = 'wechat_admin_menu_data_cache';

    public static function tableName()
    {
        return '{{%wechat_admin_menu}}';
    }
}