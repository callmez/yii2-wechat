<?php
namespace callmez\wechat\controllers;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\Controller;
use yii\base\InvalidCallException;
use yii\web\BadRequestHttpException;
use callmez\wechat\components\Wechat;
use callmez\wechat\models\RuleKeyword;

class ApiController extends Controller
{
    /**
     * 微信服务器请求内容
     * @var array
     */
    public $message = [];
    /**
     * 微信SDK类
     * @var object
     */
    public $wechat;

    /**
     * 解析微信请求的消息, 并分配action
     * @return array
     * @throws \yii\web\BadRequestHttpException
     */
    public function actions()
    {
        $return = [];

        $request = Yii::$app->request;
        $response = Yii::$app->response;

        if ($request->method == 'GET') {
            $response->content = $request->getQueryParam('echostr');
            return Yii::$app->end();
        } elseif ($request->method == 'POST') {
            if (($this->message = $this->parseRequest()) === []) {
                $response->content = 'Request Failed';
                return Yii::$app->end();
            }
            Yii::info($this->message, __METHOD__);

            $this->wechat = Wechat::createByCondition(['hash' => $request->getQueryParam('hash')]);
            if (!$this->wechat || !$this->wechat->checkSignature()) {
                $response->content = 'Access Denied!';
                return Yii::$app->end();
            }

            $params = $this->match();
            foreach ($params as $param) {
                if (!isset($param['processor'])) {
                    continue;
                } elseif (strpos($param['processor'], '\\') === false && Yii::$app->hasModule($param['processor'])) {
                    $module = Yii::$app->getModule($param['processor']);
                    $actionClass = ltrim($module->controllerNamespace . '\wechat\ApiAction', '\\');
                } else {
                    $actionClass = $param['processor'];
                }
                if (is_subclass_of($actionClass, Action::className())) {
                    Yii::info($param, __METHOD__);
                    $return[$this->defaultAction] = $actionClass;
                    break;
                }
            }
        }
        return $return;
    }

    /**
     * 解析微信请求内容
     * @param string $message
     * @return object
     */
    public function parseRequest($message = null)
    {
        $return = [];
        $message === null && $message = Yii::$app->request->getRawBody();
        if (!empty($message) && $xml = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA)) {
            foreach($xml as $k => $v) {
                if (in_array($k, ['FromUserName', 'ToUserName'])) {
                    $k = str_replace('UserName', '', $k);
                }
                $k[0] = strtolower($k[0]);
                $return[$k] = strval($v);
            }
        }
        return $return;
    }

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
     *     'title' => 'test title',
     *     'description' => 'test description',
     *     'picUrl' => 'pic url',
     *     'url' => 'link'
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
     * 输入xml内容
     * @param array $data
     * @return array
     */
    public function response(array $data)
    {
        $data = array_merge([
            'FromUserName' => $this->message['to'],
            'ToUserName' => $this->message['from']
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

    /**
     * Wechat请求匹配
     * @return array|mixed
     */
    private function match()
    {
        $params = [];
        if ($this->message['msgType'] == 'event') {
            $method = 'matchEvent' . $this->message['event'];
        } else {
            $method = 'match' . $this->message['msgType'];
        }
        if (method_exists($this, $method)) {
            $params = call_user_func([$this, $method]);
        } else {

        }
        return $params;
    }

    /**
     * 点击菜单拉取消息时的事件推送
     */
    private function matchEventClick()
    {
        $content = $this->message['eventKey']; // 匹配点击事件Key值
        if (empty($this->message['eventKey'])) {
            return [];
        }
        $this->message['content'] = $this->message['eventKey'];
        return $this->matchText(); // 作为关键字处理
    }

    /**
     * 扫描带参数二维码事件
     */
    private function matchEventSubscribe()
    {
        $params = [];
        //扫描带参数二维码事件
        if (strpos($this->message['eventKey'], 'qrscene') !== false) {
            list(, $this->message['eventkey']) = explode('_', $this->message['eventKey']);
            $params = $this->matchEventScan(); // 扫描事件处理
        }
        return $params;
    }

    /**
     * 扫码事件
     */
    private function matchEventScan()
    {
        return [];
    }

    /**
     * 位置事件
     */
    private function matchEventLocation()
    {
        return [];
    }

    /**
     * 文本消息
     */
    private function matchText()
    {
        $params = [];
        $models = RuleKeyword::findAllByKeyword($this->message['content'], $this->wechat->model->id);
        if (!empty($models)) {
            foreach ($models as $model) {
                $params[] = [
                    'rule' => $model->rid,
                    'priority' => $model->order,
                    'keyword' => $model,
                    'weid' => $model->rule->wid,
                    'processor' => $model->processor
                ];
            }
        }
        return $params;
    }

    /**
     * 图片消息
     */
    private function matchImage()
    {
        return [];
    }

    /**
     * 语音消息
     */
    private function matchVoice()
    {
        return [];
    }

    /**
     * 视频消息
     */
    private function matchVideo()
    {
        return [];
    }

    /**
     * 位置消息
     */
    private function matchLocation()
    {
        return [];
    }

    /**
     * 链接消息
     */
    private function matchLink()
    {
        return [];
    }
}