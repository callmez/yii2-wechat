<?php
namespace callmez\wechat\controllers;

use yii\web\Controller;
use yii\base\Application;
use yii\helpers\ArrayHelper;
use callmez\wechat\models\Fans;
use callmez\wechat\models\MessageHistory;
use callmez\wechat\models\ReplyRuleKeyword;
use callmez\wechat\components\ProcessController;

class FansController extends ProcessController
{
    /**
     * 粉丝相关请求记录
     */
    public function actionRecord()
    {
        $this->api->on(ApiController::EVENT_AFTER_PROCESS, [$this, 'recordProcess']);
        ArrayHelper::remove($this->message, 'Event') == 'unsubscribe' ? $this->recordUnsubscribe() : $this->recordSubscribe();
    }

    /**
     * 记录粉丝的请求和系统响应内容
     * @param $event
     * @throws \yii\base\InvalidConfigException
     */
    public function recordProcess($event)
    {
        $history = new MessageHistory();
        $attributes = [
            'wid' => $this->getWechat()->id,
            'module' => $this->getModuleName($this->api->lastProcessController),
        ];

        // 记录微信请求内容
        $_history = clone $history;
        $keyword = ArrayHelper::getValue($this->api->lastProcessParams, 'keyword');
        if ($keyword instanceof ReplyRuleKeyword) {
            $attributes['rid'] = $keyword->rid;
            $attributes['kid'] = $keyword->id;
        }
        $_history->setAttributes(array_merge($attributes, [
            'open_id' => $this->message['FromUserName'],
            'message' => $this->message,
            'type' => MessageHistory::TYPE_REQUEST
        ]));
        $_history->save();

        // 记录微信请求后系统响应的内容
        if (is_array($event->result)) {
            $_history = clone $history;
            $_history->setAttributes(array_merge($attributes, [
                'open_id' => $event->result['ToUserName'],
                'message' => $event->result,
                'type' => MessageHistory::TYPE_RESPONSE
            ]));
            $_history->save();
        }
    }
    
    /**
     * 记录粉丝关注
     * @throws \yii\base\InvalidConfigException
     */
    public function recordSubscribe()
    {
        if (!($this->getFans())) {
            $fans = new Fans();
            $fans->setAttributes([
                'wid' => $this->getWechat()->id,
                'open_id' => $this->message['FromUserName'],
                'status' => Fans::STATUS_SUBSCRIBED
            ]);
            $fans->save();
        }
    }

    /**
     * 记录粉丝取消关注
     */
    public function recordUnsubscribe()
    {
        if ($fans = $this->getFans()) {
            $fans->unsubscribe();
        }
    }

    protected function getModuleName(Controller $controller)
    {
        $name = [];
        $module = $controller->module;
        while (!$module instanceof Application) { // 判断是module而不是app
            $name[] = $module->id;
            $module = $module->module;
        }
        return implode('/', $name);
    }
}