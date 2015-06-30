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
     * 用户为主界面
     */
    const INTERACTION_USER = 'user';
    /**
     * 服务商为主界面
     */
    const INTERACTION_SERVER = 'server';
    /**
     * 默认为服务商主界面, 呈现效果将以服务商为第一视角
     * @var string
     */
    public $interaction = self::INTERACTION_SERVER;
    /**
     * @inheritdoc
     */
    public $layout = "\n{summary}\n{items}\n<div class=\"text-center\">\n{pager}\n</div>\n";
    /**
     * @inheritdoc
     */
    public $itemView = '@callmez/wechat/widgets/views/messageList';
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'wechat-message-list'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // 信息记录一般从尾页开始显示
        $pagination = $this->dataProvider->getPagination();
        $params = $pagination->params !== null ? $pagination->params : Yii::$app->getRequest()->getQueryParams();
        if (!(isset($params[$pagination->pageParam]) && is_scalar($params[$pagination->pageParam]))) {
            $pageSize = $pagination->getPageSize();
            $totalCount = $this->dataProvider->getTotalCount();
            $pagination->setPage((int) (($totalCount + $pageSize - 1) / $pageSize) - 1);
        }
    }
}
