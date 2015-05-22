<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

/**
 * 微信模块Asset
 * @package callmez\wechat\assets
 */
class WechatAsset extends AssetBundle
{
    public $sourcePath = '@callmez/wechat/web';
    public $css = [
        'css/wechat.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'callmez\wechat\assets\FontAwesomeAsset',
    ];
}