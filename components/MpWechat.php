<?php
namespace callmez\wechat\components;

use callmez\wechat\models\Wechat;
use callmez\wechat\models\MessageHistory;

/**
 * 微信公众号SDK, 增加微信公众号数据库操作
 * @package callmez\wechat\components
 */
class MpWechat extends \callmez\wechat\sdk\MpWechat
{
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

    /**
     * 跳转微信网页获取用户授权信息
     * @param string $state
     * @return mixed
     */
    public function getAuthorizeUserInfo($state = 'authorize', $scope = 'snsapi_base')
    {
        $request = Yii::$app->request;
        if (($code = $request->get('code')) && $request->get('state') == $state) {
            return $this->getOauth2AccessToken($code);
        } else {
            Yii::$app->getResponse()->redirect($this->getOauth2AuthorizeUrl($request->getAbsoluteUrl(), $state, $scope));
            Yii::$app->end();
        }
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(array $data)
    {
        $result = parent::sendMessage($data);
        if ($result && isset($data['touser'])) { // 记录发送日志
            MessageHistory::add([
                'wid' => $this->model->id,
                'from' => $this->model->original,
                'to' => $data['touser'],
                'message' => $data,
                'type' => MessageHistory::TYPE_CUSTOM
            ]);
        }
        return $result;
    }
}