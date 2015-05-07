<?php
namespace callmez\wechat\behaviors;

use yii\base\Behavior;

/**
 * 事件behavior.使用此类可以方便注册事件
 * @package callmez\wechat\behaviors
 */
class EventBehavior extends Behavior
{
    /**
     * ~~~
     * [
     *     Model::EVENT_BEFORE_VALIDATE => [$object, 'funcName'],
     *     Model::EVENT_AFTER_VALIDATE =>  [$object, 'funcName'],
     * ]
     * ~~~
     * @var array
     */
    public $events = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return $this->events;
    }
}