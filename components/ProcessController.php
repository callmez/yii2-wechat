<?php
namespace callmez\wechat\components;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use callmez\wechat\models\Fans;
use callmez\wechat\components\BaseController;
use callmez\wechat\controllers\ApiController;

/**
 * 微信请求处理基类
 * 所有微信服务请求过来的处理类必须继承该类
 * @package callmez\wechat\components
 */
class ProcessController extends BaseController
{
    /**
     * 微信请求关闭CSRF验证
     * @var bool
     */
    public $enableCsrfValidation = false;
    /**
     * 微信请求消息
     * @var Object
     */
    public $message;
    /**
     * API处理Controller
     * @var Object
     */
    public $api;

    public function init()
    {
        $this->api = Yii::$app->requestedAction->controller;
        if (!($this->api instanceof ApiController)) { // 必须是从callmez\Wechat\controllers\ApiController引导
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $this->message = &$this->api->message;
        $this->setWechat($this->api->getWechat());

        parent::init();
    }

    /**
     * @var \callmez\wechat\models\Wechat;
     */
    private $_wechat;
    /**
     * 正常的微信请求会通过callmez\wechat\controllers\ApiController实例并传入Wechat model
     * @param Wechat $wechat
     */
    public function setWechat(\callmez\wechat\models\Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            throw new InvalidConfigException('The wechat model property must be set.');
        }
        return $this->_wechat;
    }

    /**
     * @var \callmez\wechat\models\Fans
     */
    private $_fans = false;
    /**
     * 获取触发微信请求的微信用户信息
     * @return Fans
     */
    public function getFans()
    {
        if ($this->_fans === false) {
            $this->_fans = Fans::findByOpenId($this->message['FromUserName']);
        }
        return $this->_fans;
    }

    /**
     * 响应文本消息
     * 例: $this->responseText('hello world');
     * @param $content
     * @return array
     */
    public function responseText($content)
    {
        return [
            'MsgType' => 'text',
            'Content' => $content
        ];
    }

    /**
     * 响应图文消息
     * 例: $this->responseNews([
     *     [
     *         'title' => 'test title',
     *         'description' => 'test description',
     *         'picUrl' => 'pic url',
     *         'url' => 'link'
     *     ],
     *      ...
     * ]);
     * @param array $articles
     * @return array
     */
    public function responseNews(array $articles)
    {
        if (isset($articles['title'])) {
            $articles = [$articles];
        }
        $response = [
            'MsgType' => 'news',
            'ArticleCount' => count($articles),
        ];
        foreach ($articles as $article) {
            $response['Articles'][] = [
                'Title' => $article['title'],
                'Description' => $article['description'],
                'PicUrl' => $article['picUrl'],
                'Url' => $article['url']
            ];
        }
        return $response;
    }

    /**
     * 响应图片消息
     * @param $mid 图片mid(需先上传图片给wechat服务器获得mid)
     * 例: $this->responseImage([
     *     'mid' => '123456'
     * ])
     * @return array
     */
    public function responseImage($mid)
    {
        return [
            'MsgType' => 'image',
            'Image' => [
                'MediaId' => $mid
            ]
        ];
    }

    /**
     * 响应语音消息
     * @param $mid 语音mid(需先上传语音给wechat服务器获得mid)
     * 例: $this->responseVoice([
     *     'mid' => '123456'
     * ])
     * @return array
     */
    public function responseVoice($mid)
    {
        return [
            'MsgType' => 'voice',
            'Image' => [
                'MediaId' => $mid
            ]
        ];
    }

    /**
     * 响应视频消息
     * 例: $this->responseVideo([
     *     'mid' => '123456',
     *     'thumbMid' => '1234567'
     * ])
     * @param array $video mid(需先上传视频给wechat服务器获得mid和thumbMid)
     * @return array
     */
    public function responseVideo(array $video)
    {
        return [
            'MsgType' => 'video',
            'Video' => [
                'MediaId' => $video['mid'],
                'ThumbMediaId' => $video['thumbMid']
            ]
        ];
    }

    /**
     * 响应音乐消息
     * 例: $this->responseMusic([
     *     'title' => 'music title',
     *     'description' => 'music description',
     *     'musicUrl' => 'music link',
     *     'hgMusicUrl' => 'HQ music link', // 选填,
     *     'thumbMid' = '123456'
     * ])
     * @param array $music
     * @return array
     */
    public function responseMusic(array $music)
    {
        return [
            'MsgType' => 'music',
            'Image' => [
                'Title' => $music['title'],
                'Description' => $music['description'],
                'MusicUrl' => $music['musicUrl'],
                'HQMusicUrl' => isset($music['hqMusicUrl']) ? $music['hqMusicUrl'] : $music['musicUrl'],
                'ThumbMediaId' => $music['thumbMid']
            ]
        ];
    }
}
