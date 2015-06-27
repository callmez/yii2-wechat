<?php
namespace callmez\wechat\models;

use yii\base\Model;

/**
 * 图文素材表单
 * @package callmez\wechat\models
 */
class MediaNewsForm extends Model
{
    /**
     * 标题
     * @var string
     */
    public $title;
    /**
     * 图文消息封面图片素材
     * @var string
     */
    public $thumbMediaId;
    /**
     * 作者
     * @var string
     */
    public $author;
    /**
     * 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     * @var string
     */
    public $digest;
    /**
     * 是否显示封面
     * @var bool
     */
    public $showCoverPic;
    /**
     * 图文消息的具体内容
     * @var string
     */
    public $content;
    /**
     * 图文消息的原文地址
     * @var string
     */
    public $contentSourceUrl;

    public function rules()
    {
        return [];
    }
}