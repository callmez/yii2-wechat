<?php

namespace callmez\wechat;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * 微信启动预设
 * @package callmez\wechat
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('gii')) { // 增加gii生成器
            $gii = $app->getModule('gii');

            if (array_key_exists('wechat', $gii->generators)) { // 微信生成器
                $gii->generators['wechat'] = [
                    'class' => 'callmez\wechat\Generator'
                ];
            }

            if (array_key_exists('ajaxcrud', $gii->generators)) { // AJAX-CRUD
                $gii->generators['ajaxcrud'] = [
                    'class' => 'callmez\ajaxcrud\Generator'
                ];
            }
        }
    }
}