<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

/**
 * 微信消息发送交互处理
 * @package callmez\wechat\assets
 */
class MessageAsset extends AssetBundle
{
    public $sourcePath = '@callmez/wechat/web';
    public $css = [
        'css/wechat.css'
    ];
    public $js = [
        'js/message.js'
    ];
    public $depends = [
        'callmez\wechat\assets\WechatAsset',
    ];
}