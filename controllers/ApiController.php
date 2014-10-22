<?php
namespace yii\wechat\controllers;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\Controller;
use yii\base\InvalidCallException;
use yii\web\BadRequestHttpException;
use yii\wechat\components\Wechat;
use yii\wechat\models\RuleKeyword;

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

    public function actions()
    {
        $return = [];
        $this->wechat = Wechat::createByCondition(['hash' => Yii::$app->request->getQueryParam('hash')]);
        if ($this->wechat !== null && $this->wechat->checkSignature()) {
            if (($this->message = $this->parseRequest()) === []) {
                throw new BadRequestHttpException('Request parse failed.');
            }
            $params = $this->match();
            foreach ($params as $param) {
                if (isset($param['processor'])) {
                    if (strpos($param['processor'], '\\') === false && Yii::$app->hasModule($param['processor'])) {
                        $module = Yii::$app->getModule($param['processor']);
                        $actionClass = ltrim($module->controllerNamespace . '\wechat\ApiAction', '\\');
                    } else {
                        $actionClass = $param['processor'];
                    }
                    if (is_subclass_of($actionClass, Action::className())) {
                        $return[$this->defaultAction] = $actionClass;
                    }
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

    public function responseText($content)
    {

    }

    public function responseNews(array $articles)
    {

    }

    public function responseImage($mid)
    {

    }

    public function responseVoice($mid)
    {

    }

    public function responseMusic(array $music)
    {

    }

    public function response(array $data)
    {
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