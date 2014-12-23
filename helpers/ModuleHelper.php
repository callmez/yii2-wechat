<?php
namespace callmez\wechat\helpers;

use Yii;
use Symfony\Component\Yaml\Yaml;

class ModuleHelper
{
    /**
     * 获取微信扩展模块命名空间
     * 微信扩展模块: 放置在@app/modules/wechat/modules下
     * 专用模块微信扩展: **设置**为该模块下的modules/wechat目录 (优先级会高于微信扩展模块)
     * @param $name
     * @return string
     */
    public static function getWechatModuleNamespace($name)
    {
        if (Yii::$app->hasModule($name)) {
            $namespace = 'app\\modules\\' . $name . '\\modules\\wechat';
        } else {
            $namespace = 'app\\modules\\wechat\\modules\\' . $name;
        }
        return str_replace('/', '\\', $namespace);
    }
}