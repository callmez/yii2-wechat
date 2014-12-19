<?php
namespace callmez\wechat\helpers;

use Yii;

class ModuleHelper
{
    /**
     * 获取微信扩展模块命名空间
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

    /**
     * 查找微信扩展模块
     */
    public static function findWechatModules($path = '@app/modules')
    {
        $modules = [];
        $callback = function($dir, $file) use (&$modules) {
            $path = $dir . '/' . $file;
            if (!file_exists($path . '/wechat.yml')) {
                return ;
            }
            $modules[] = [
                'name' => $file,
                'path' => $path
            ];
        };
        static::readDir(Yii::getAlias($path), function($dir, $file) use ($callback) {
            $callback($dir, $file);
            $dir = $dir . '/' . $file . '/modules';
            if (!is_dir($dir)) {
                return ;
            }
            static::readDir($dir, $callback);
        });
        return $modules;
    }

    protected static function readDir($dir, \Closure $callback)
    {
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $callback($dir, $file);
        }
        closedir($handle);
    }
}