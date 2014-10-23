<?php

namespace callmez\wechat\controllers;
use callmez\wechat\models\Wechat;

/**
 * 微信模拟器
 * @package app\controllers
 */
class SimulatorController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $typeArray = [
            'text' => [
                'label' => '文本',
                'target' => 'content'
            ],
            'image' => [
                'label' => '图片',
                'target' => 'pic_url'
            ],
            'location' => [
                'label' => '位置',
                'target' => ['location_x', 'location_y']
            ],
            'link' => [
                'label' => '链接',
                'target' => 'url'
            ],
            'event' => [
                'label' => '菜单',
                'target' => 'event_key'
            ],
            'subscribe' => [
                'label' => '关注'
            ],
            'unsubscribe' => [
                'label' => '取消关注'
            ],
            'other' => [
                'label' => '其他'
            ]
        ];
        return $this->render('index', [
            'typeArray' => $typeArray,
            'wechats' => Wechat::find()->asArray()->indexBy('id')->all()
        ]);
    }

}
