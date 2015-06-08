<?php
namespace callmez\wechat\components;

use callmez\wechat\models\Wechat;

/**
 * 微信公众号SDK, 增加微信公众号数据库操作
 * @package callmez\wechat\components
 */
class MpWechat extends \callmez\wechat\sdk\MpWechat
{
    use WechatTrait;
    /**
     * 绑定的公众号存储类
     * @var Wechat
     */
    protected $model;

    /**
     * @param Wechat $wechat
     * @param array $config
     */
    public function __construct(Wechat $wechat, $config = [])
    {
        $this->model = $wechat;
        $config = array_merge([
            'appId' => $this->model->key,
            'appSecret' => $this->model->secret,
            'token' => $this->model->token,
            'encodingAesKey' => $this->model->encoding_aes_key
        ], $config);

        parent::__construct($config);
    }
}