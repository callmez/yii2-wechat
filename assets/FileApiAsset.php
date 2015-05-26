<?php

namespace callmez\wechat\assets;

use Yii;
use yii\web\View;
use yii\helpers\Json;
use yii\web\AssetBundle;

class FileApiAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery.fileapi';
    public $js = [
        'FileAPI/FileAPI.min.js',
        'FileAPI/FileAPI.exif.js',
        'jquery.fileapi.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * FileApi 全局设置
     * @var array
     */
    public $settings = [
        'withCredentials' => false // 默认开启跨域
    ];

    /**
     * 注册FileAPI默认设置
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $view = Yii::$app->controller->getView();
        $settings = array_merge([
            'debug' => YII_DEBUG ? 1: 0,
            'staticPath' => Yii::getAlias($this->baseUrl)
        ], $this->settings);
        $view->registerJs('window.FileAPI=' . Json::encode($settings) . ';', View::POS_HEAD);
        parent::registerAssetFiles($view);
    }
}