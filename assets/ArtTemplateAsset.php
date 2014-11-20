<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

class ArtTemplateAsset extends AssetBundle
{
    public $sourcePath = '@npm/art-template';
    public $js = [
        'dist/template.js',
    ];
}