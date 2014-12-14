<?php
namespace callmez\wechat;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if (!isset($app->modules['wechat'])) {
            return ;
        }
        // 注册微信模块生成器
        if ($app->hasModule('gii')) {
            $gii = $app->getModule('gii');
            $gii->generators = array_merge([
                'wechatModule' => [
                    'class' => 'callmez\wechat\generators\module\Generator'
                ]
            ], $gii->generators);
        }
    }
}