<?php
namespace callmez\wechat\controllers;

use Yii;
use callmez\wechat\models\Fans;
/**
 * 微信请求默认处理
 * @package callmez\wechat\controllers
 */
class ProcessController extends \callmez\wechat\components\ProcessController
{

    /**
     * 关注处理
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSubscribe()
    {
        //存储粉丝信息
        if (!$this->getFans()) {
            $fans = Yii::createObject(Fans::className());
            $fans->setAttributes([
                'wid' => $this->getWechat()->id,
                'open_id' => $this->message['FromUserName'],
                'status' => Fans::STATUS_SUBSCRIBED
            ]);
            $fans->save();
        }
    }

    /**
     * 取消关注处理
     */
    public function actionUnsubscribe()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }
}