<?php
namespace callmez\wechat\components;

use yii\base\Event;

class ProcessorEvent extends Event
{
    public $message;

    public $wechat;

    public $result;

    public $isValid = true;


    public function __construct(\stdClass $message, Wechat $wechat, $config = [])
    {
        $this->message = $message;
        $this->wechat = $wechat;
        parent::__construct($config);
    }
}