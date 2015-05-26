<?php

namespace callmez\wechat\assets;

use yii\web\AssetBundle;

/**
 * Bootstrap UI 时间选取控件
 * @package callmez\wechat\assets
 */
class BoostrapDateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/smalot-bootstrap-datetimepicker';
    public $css = [
        'css/bootstrap-datetimepicker.css'
    ];
    public $js = [
        'js/bootstrap-datetimepicker.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}