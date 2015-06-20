<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use callmez\wechat\assets\WechatAsset;
use callmez\wechat\widgets\MessageList;
use callmez\wechat\models\MessageHistory;

$wechaatAsset = WechatAsset::register($this);
if (
    $widget->interaction == MessageList::INTERACTION_SERVER && $model->type == MessageHistory::TYPE_REQUEST ||
    $widget->interaction == MessageList::INTERACTION_USER && in_array($model->type, [MessageHistory::TYPE_RESPONSE, MessageHistory::TYPE_CUSTOM])
) {
    $type = 'receive';
} else {
    $type = 'send';
}
if (
    $widget->interaction == MessageList::INTERACTION_SERVER && $model->type == MessageHistory::TYPE_REQUEST ||
    $widget->interaction == MessageList::INTERACTION_USER && in_array($model->type, [MessageHistory::TYPE_RESPONSE, MessageHistory::TYPE_CUSTOM])
) {
    $avatar = '/images/avatar.jpg';
} else {
    $avatar = '/images/wechat.jpg';
}
// TODO 完成所有类型信息显示
?>
<div class="message <?= $type ?>" data-toggle="tooltip" title="<?= date('Y-m-d H:i:s', $model->created_at) ?>">
    <img class="avatar" src="<?= $wechaatAsset->baseUrl . $avatar ?>" />
    <?php if ($model->type == MessageHistory::TYPE_CUSTOM): ?>
        <div class="content <?= $model->message['msgtype'] ?>">
            <?php switch ($model->message['msgtype']) { // TODO 完成所有类型信息显示
                case 'text':
                    echo $model->message['text']['content'];
                    break;
                case 'image':
                    echo '[图片]';
                    break;
                case 'voice':
                    echo '[音频信息]';
                    break;
                case 'video':
                    echo '[视频信息]';
                    break;
                case 'music':
                    echo '[音乐信息]';
                    break;
                case 'news':
                    echo '[图文信息]';
                    break;
                default:
                    echo '[信息显示错误!]';
            } ?>
        </div>
    <?php else: ?>
        <div class="content <?= $model->message['MsgType'] ?>">
            <?php switch ($model->message['MsgType']) {
                case 'text':
                    echo $model->message['Content'];
                    break;
                case 'image':
                    echo Html::img($model->message['PicUrl']);
                    break;
                case 'voice':
                    echo '[音频信息]';
                    break;
                case 'video':
                    echo '[视频信息]';
                    break;
                case 'shortvideo':
                    echo '[小视频信息]';
                    break;
                case 'location':
                    echo '[地理位置信息]';
                    break;
                case 'link':
                    echo  '[链接消息]';
                    break;
                case 'event':
                    switch ($model->message['Event']) {
                        case 'subscribe':
                            echo '[' . (strexists($this->message['Eventkey'], 'qrscene')  ? '扫码' : '') . '关注事件]';
                            break;
                        case 'unsubscribe':
                            echo '[取消关注事件]';
                            break;
                        case 'SCAN':
                            echo '[扫码事件]';
                            break;
                        case 'LOCATION':
                            echo '[上报地理位置事件]';
                            break;
                        case 'CLICK':
                            echo '[点击自定义菜单事件]';
                            break;
                        case 'VIEW':
                            echo '[点击自定义菜单跳转链接事件]';
                            break;
                        default:
                            echo '[事件显示错误!]';
                    }
                    break;
                case 'news':
                    echo '[图文信息]';
                    break;
                default:
                    echo '[信息显示错误!]';
            } ?>
        </div>
    <?php endif ?>
</div>