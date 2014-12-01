<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularDragonDropAsset extends AssetBundle
{
    public $sourcePath = '@bower/angular-dragon-drop';
    public $js = [
        'dragon-drop.js',
    ];
    public $depends = [
        'callmez\wechat\assets\AngularAsset',
    ];
}