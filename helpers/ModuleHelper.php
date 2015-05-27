<?php
namespace callmez\wechat\helpers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use Symfony\Component\Yaml\Yaml;
use callmez\wechat\Module;
use callmez\wechat\models\Wechat;
use callmez\wechat\components\BaseModule;
use callmez\wechat\models\Module as ModuleModel;

class ModuleHelper
{

    /**
     * 获取API接口地址
     * @param Wechat $wechat 公众号
     * @param array $params 补充的参数
     * @param bool $scheme 完整地址,或者其他协议完整地址
     * @return string
     */
    public static function getApiUrl(Wechat $wechat, array $params = [], $scheme = false)
    {
        $token = $wechat->token;
        $nonce = Yii::$app->security->generateRandomString(5);
        $signArray = [$token, TIMESTAMP, $nonce];
        sort($signArray, SORT_STRING);
        $signature = sha1(implode($signArray));
        return Url::to(array_merge([
            '/wechat/' . Yii::$app->getModule('wechat')->apiRoute,
            'timestamp' => TIMESTAMP,
            'nonce' => $nonce,
            'signature' => $signature
        ], $params), $scheme);
    }

    /**
     * 获取扩展模块的后台链接
     * @param $id 扩展模块的ID
     * @return array
     */
    public static function getAdminHomeUrl($id)
    {
        return ['/wechat/' . $id . '/admin' ];
    }

    /**
     * 是否扩展模块
     * @param $module
     * @return bool
     */
    public static function isAddonModule($module)
    {
        return is_subclass_of($module, BaseModule::className());
    }

    /**
     * 获取扩展模块基本命名空间
     * @param Module|array $module
     * @return bool|string
     */
    public static function getBaseNamespace($module)
    {
        $type = ArrayHelper::getValue($module, 'type');
        $id = ArrayHelper::getValue($module, 'id');
        if ($type && $id) {
            $path = $type == ModuleModel::TYPE_ADDON ? Module::ADDON_MODULE_PATH : Module::CORE_MODULE_PATH;
            return str_replace('/', '\\', ltrim($path, '@')) . '\\' . $id;
        }
        return false;
    }

    /**
     * 根据ID查找可用的模块
     * @param $id
     * @return null
     */
    public static function findAvailableModuleById($id)
    {
        $availableModels = static::findAvailableModules();
        return array_key_exists($id, $availableModels) ? $availableModels[$id] : null;
    }

    /**
     * 查找可用的插件模块
     * @return mixed
     */
    public static function findAvailableModules()
    {
        return static::scanAvailableModules([Module::ADDON_MODULE_PATH, Module::CORE_MODULE_PATH]);
    }

    /**
     * 扫描可用的插件模块
     *
     * 该方法是严格按照Yii的模块路径规则(比如@app/modules, @app/mdoules/example/modules)来查找模块
     * 如果您的模块有特殊路径需求. 可能正确安装不了, 建议按照规则设计扩展模块
     *
     * @param array|string $paths
     * @return array
     */
    protected static function scanAvailableModules($paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        $modules = [];
        foreach ($paths as $path) {
            $path = Yii::getAlias($path);
            if (is_dir($path) && (($handle = opendir($path)) !== false)) {
                while (($file = readdir($handle)) !== false) {
                    if (in_array($file, ['.', '..']) || !is_dir($currentPath = $path . DIRECTORY_SEPARATOR . $file)) {
                        continue;
                    }
                    // 是否有wechat.yml安装配置文件
                    $settingFile = $currentPath . DIRECTORY_SEPARATOR . 'wechat.yml';
                    if (file_exists($settingFile)) {
                        $class = Yii::$app->getModule('wechat')->moduleModelClass;
                        $model = new $class;
                        $model->setAttributes(Yaml::parse(file_get_contents($settingFile)));
                        if ($model->id == $file && $model->validate()) { // 模块名必须等于目录名并且验证模块正确性
                            $modules[$model->id] = $model;
                        }
                    }
                }
            }
        }
        return $modules;
    }
}