<?php
namespace callmez\wechat\widgets;

use Yii;
use yii\widgets\ListView;

/**
 * 微信消息列表模拟显示
 */
class MessageList extends ListView
{
    /**
     * @inheritdoc
     */
    public $layout = "{summary}\n{items}\n<div class=\"text-center\">\n{pager}\n</div>";
    /**
     * @inheritdoc
     */
    public $itemView = '@callmez/wechat/widgets/views/messageList';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'wechat-message-list'];
}
