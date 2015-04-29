<?php
namespace callmez\wechat\models;

use yii\base\Model;

/**
 * 客服信息发送类
 * @package callmez\wechat\models
 */
class CustomMessage extends Model
{
    /**
     * 发送用户微信ID
     * @var sting|array
     */
    public $toUser;
    /**
     * 发送信息类型
     * @var 信息类型
     */
    public $msgType = 'text';
    /**
     * 文本信息内容
     * @var string
     */
    public $content;
    /**
     * 图片, 声音, 视频信息媒体ID
     * @var string
     */
    public $mediaId;
    /**
     * 视频信息缩略图媒体ID
     * @var string
     */
    public $thumbMediaId;
    /**
     * 视频,音频信息标题
     * @var string
     */
    public $title;
    /**
     * 视频,音频信息描述
     * @var string
     */
    public $description;
    /**
     * 音频信息链接
     * @var string
     */
    public $musicUrl;
    /**
     * 音频信息高品质链接
     * @var string
     */
    public $hqMusicUrl;
    /**
     * 信息可用发送类型
     * @var array
     */
    public static $messageTypes = [
        'text' => '文本',
        'image' => '图片',
        'voice' => '语音',
        'video' => '视频',
        'music' => '音乐',
        'news' => '图文'
    ];

    public function rules()
    {
        return [
            [['toUser', 'msgType'], 'required'],

            [['content'], 'required', 'when' => function($model) {
                return $model->msgType == 'text';
            }],

            // TODO 完成其他类型的规则验证
            [['mediaId', 'thumbMediaId', 'title', 'description', 'musicUrl', 'hqMusicUrl'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'msgType' => '消息类型',
            'content' => '消息内容',
        ];
    }


}