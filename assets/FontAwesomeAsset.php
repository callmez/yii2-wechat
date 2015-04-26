<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

/**
 * Font Awesome
 * @package callmez\wechat\assets
 */
class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/font-awesome';
    public $css = [
        'css/font-awesome.css'
    ];
}