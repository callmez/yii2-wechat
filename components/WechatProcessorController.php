<?php
namespace callmez\wechat\components;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;

/**
 * 微信消息处理控制类, 微信消息类服务需继承此类
 * @package callmez\wechat\components
 */
class WechatProcessorController extends WechatController
{
    use ApiTrait;
    /**
     * 微信请求关闭csrf验证
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * 响应文本消息
     * 例: $this->responseText('hello world');
     * @param $content
     * @return array
     */
    public function responseText($content)
    {
        return $this->response([
            'MsgType' => 'text',
            'Content' => $content
        ]);
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
     *     [
     *      ...
     *     ]
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
        return $this->response($response);
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
        return $this->response([
            'MsgType' => 'image',
            'Image' => [
                'MediaId' => $mid
            ]
        ]);
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
        return $this->response([
            'MsgType' => 'voice',
            'Image' => [
                'MediaId' => $mid
            ]
        ]);
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
        return $this->response([
            'MsgType' => 'video',
            'Video' => [
                'MediaId' => $video['mid'],
                'ThumbMediaId' => $video['thumbMid']
            ]
        ]);
    }

    /**
     * 响应音频消息
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
        return $this->response([
            'MsgType' => 'music',
            'Image' => [
                'Title' => $music['title'],
                'Description' => $music['description'],
                'MusicUrl' => $music['musicUrl'],
                'HQMusicUrl' => isset($music['hqMusicUrl']) ? $music['hqMusicUrl'] : $music['musicUrl'],
                'ThumbMediaId' => $music['thumbMid']
            ]
        ]);
    }

    /**
     * 输出xml内容
     * @param array $data
     * @return array
     */
    public function response(array $data)
    {
        $data = array_merge([
            'FromUserName' => $this->message->to,
            'ToUserName' => $this->message->from
        ], $data);
        Yii::info($data, __METHOD__);
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_XML;
        if (is_array($response->formatters[$response->format])) {
            $response->formatters[$response->format]['rootTag'] = 'xml';
            $response->formatters[$response->format]['contentType'] = 'text/html';
        } else {
            $response->formatters[$response->format] = [
                'class' => $response->formatters[$response->format],
                'rootTag' => 'xml',
                'contentType' => 'text/html',
            ];
        }
        return $data;
    }
}