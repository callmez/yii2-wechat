<?php
namespace callmez\wechat\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;

/**
 * ActiveRecord字段 数组 <=> 字符串 转换
 *
 * ```php
 * public function behaviors(){
 *     return [
 *         'event' => [
 *             'class' => ArrayBehavior::className(),
 *             'attributes' => [
 *                 ArrayBehavior::TYPE_SERIALIZE => ['attribute1', 'attribute2'],
 *             ]
 *          ]
 *      ];
 *  }
 * ```
 *
 * @package callmez\wechat\behaviors
 */
class ArrayBehavior extends Behavior
{
    /**
     * 序列化转换
     */
    const TYPE_SERIALIZE = 'serialize';
    /**
     * json转换
     */
    const TYPE_JSON = 'json';
    /**
     * 触发操作的事件
     * @var array
     */
    public $events = [
        ActiveRecord::EVENT_BEFORE_INSERT,
        ActiveRecord::EVENT_BEFORE_UPDATE,
        ActiveRecord::EVENT_AFTER_FIND
    ];

    /**
     * 需要转换的attribute
     * @var
     */
    public $attributes;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            throw new InvalidConfigException('The "attributes" property must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys($this->events, 'evaluateAttributes');
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (in_array($event->name, $this->events)) {
            foreach ($this->attributes as $type => $attributes) {
                $method = 'get' . $type . 'value';
                if (method_exists($this, $method)) {
                    foreach ( (array) $attributes as $attribute) {
                        $this->owner->$attribute = $this->$method($this->owner->$attribute, $event->name);
                    }
                }
            }
        }
    }

    /**
     * 获取序列化后的值
     * @param $value
     * @param $event
     * @return string
     */
    protected function getSerializeValue($value, $event)
    {
        switch ($event) {
            case ActiveRecord::EVENT_BEFORE_INSERT:
            case ActiveRecord::EVENT_BEFORE_UPDATE:
                if (is_array($value)) {
                    $value = serialize($value);
                }
                break;
            case ActiveRecord::EVENT_AFTER_FIND:
                $value = @unserialize($value) ?: $value;
                break;
        }
        return $value;
    }

    /**
     * 获取json转换后的值
     * @param $value
     * @param $event
     * @return mixed|\Services_JSON_Error|string
     */
    protected function getJsonValue($value, $event)
    {
        switch ($event) {
            case ActiveRecord::EVENT_BEFORE_INSERT:
            case ActiveRecord::EVENT_BEFORE_UPDATE:
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                break;
            case ActiveRecord::EVENT_AFTER_FIND:
                $value = @json_encode($value, true) ?: $value;
                break;
        }
        return $value;
    }
}