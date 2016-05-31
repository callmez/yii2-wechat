<?php

namespace callmez\wechat;

use Yii;
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
        // 定义毫秒时间戳
        defined('TIMESTAMP') or define('TIMESTAMP', $_SERVER['REQUEST_TIME']);
        // 自动注册存储目录(可以在config中配置)
        isset(Yii::$aliases['@storage']) or Yii::setAlias('@storage', Yii::getAlias('@web/storage'));
        isset(Yii::$aliases['@storageRoot']) or Yii::setAlias('@storageRoot', Yii::getAlias('@webroot/storage'));

        if ($app->hasModule('gii')) { // 增加gii生成器
            $gii = $app->getModule('gii');

            if (!array_key_exists('wechat', $gii->generators)) { // 微信生成器
                $gii->generators['wechat'] = [
                    'class' => 'callmez\wechat\generators\module\Generator'
                ];
            }

            if (!array_key_exists('ajaxcrud', $gii->generators)) { // AJAX-CRUD
                $gii->generators['ajaxcrud'] = [
                    'class' => 'callmez\ajaxcrud\generators\crud\Generator'
                ];
            }
        }
        if (!$app->hasMethod('gridview')) { // 设置 GridView模块
            $app->setModule('gridview', [
                'class' => '\kartik\grid\Module'
            ]);
        }

    }
}
