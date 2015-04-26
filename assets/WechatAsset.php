<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

/**
 * 微信通用asset
 * @package callmez\wechat\assets
 */
class WechatAsset extends AssetBundle
{
    public $sourcePath = '@callmez/wechat/web';
    public $css = [
        'css/wechat.css'
    ];
    public $depends = [
        'callmez\wechat\assets\FontAwesomeAsset',
    ];
}