<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularDragAndDropListsAsset extends AssetBundle
{
    public $sourcePath = '@bower/angular-drag-and-drop-lists';
    public $js = [
        'angular-drag-and-drop-lists.js',
    ];
    public $depends = [
        'callmez\wechat\assets\AngularAsset',
    ];
}