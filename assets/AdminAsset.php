<?php
namespace callmez\wechat\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $sourcePath = '@callmez/wechat/assets/web';

    public $css = [
        'css/admin.css'
    ];
    public $js = [
        'js/admin.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    ];

}