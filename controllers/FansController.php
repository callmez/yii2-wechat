<?php
namespace callmez\wechat\controllers;

use yii\helpers\ArrayHelper;
use callmez\wechat\models\Fans;
use callmez\wechat\components\ProcessController;

class FansController extends ProcessController
{
    /**
     * 粉丝相关请求记录
     */
    public function actionRecord()
    {
        ArrayHelper::remove($this->message, 'event') == 'unsubscribe' ? $this->actionUnsubscribe() : $this->actionSubscribe();
    }

    /**
     * 粉丝关注操作
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSubscribe()
    {
        if (!($this->getFans())) {
            $fans = new Fans();
            $fans->setAttributes([
                'wid' => $this->getWechat()->id,
                'open_id' => $this->message['fromUserName'],
                'status' => Fans::STATUS_SUBSCRIBED
            ]);
            $fans->save();
        }
    }

    /**
     * 粉丝取消关注操作
     */
    public function actionUnsubscribe()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }
}