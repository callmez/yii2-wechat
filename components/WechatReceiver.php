<?php
namespace callmez\wechat\components;

use Yii;
use yii\base\Action;
use yii\base\Object;
use yii\base\InvalidConfigException;
use callmez\wechat\models\RuleKeyword;

class WechatReceiver extends Object
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
     * 解析微信请求并返回指定的响应控制器
     * @return array
     */
    public function resolveController()
    {
        $request = Yii::$app->request;
        switch ($request->method) {
            case 'GET':
                Yii::$app->response->content = $request->getQueryParam('echostr');
                return Yii::$app->end();
            case 'POST':
                $this->wechat = $this->findWechat(); // 查找公众号
                $this->message = $this->parseRequest(); // 解析请求信息
                $controller = $this->macthController(); // 匹配控制器
                return $controller ? [
                    'class' => $controller,
                    'wechat' => $this->wechat,
                    'message' => $this->message
                ] : $controller;
            default:
                return [];
        }
    }

    /**
     * 查找微信公众号
     * @param array $condition
     * @return bool|object
     */
    protected function findWechat(array $condition = [])
    {
        $condition === [] && $condition = ['hash' => Yii::$app->request->getQueryParam('hash')];
        if (($wechat = Wechat::instanceByCondition($condition)) && $wechat->checkSignature()) {
            Yii::info($wechat, __METHOD__);
            return $wechat;
        } else {
            Yii::$app->response->content = 'Access Denied!';
            Yii::$app->end();
        }
    }

    /**
     * 解析微信请求内容
     * @param string $message
     * @return object
     */
    protected function parseRequest($message = null)
    {
        $return = [];
        if (($xml = $this->wechat->parseRequestData($message)) !== []) {
            foreach($xml as $k => $v) {
                if (in_array($k, ['FromUserName', 'ToUserName'])) { //转换成 from, to 关键字
                    $k = str_replace('UserName', '', $k);
                }
                $k[0] = strtolower($k[0]);
                $return[$k] = strval($v);
            }
        }
        if ($return == []) {
            Yii::$app->response->content = 'Request Failed!';
            Yii::$app->end();
        }
        Yii::info($this->message, __METHOD__);
        return $return;
    }

    /**
     * 匹配控制器
     */
    protected function macthController()
    {
        $controller = null;
        foreach ($this->match() as $param) {
            if (!isset($param['processor']) || empty($param['processor'])) {
                continue;
            }
            $controller = static::getProcessorController($param['processor']);
            if (is_subclass_of($controller, WechatProcessorController::className())) {
                Yii::info($param, __METHOD__);
                break;
            } elseif (YII_DEBUG) {
                throw new InvalidConfigException("Controller class {$controller} must extend from " . WechatProcessorController::className() . ".");
            }
        }
        return $controller;
    }

    /**
     * 返回接口控制器namespace
     * @param $processor
     * @return string
     */
    public static function getProcessorController($processor)
    {
        if (strpos($processor, '\\') !== false) { // 命名空间指定处理类
            $controller = $processor;
        } elseif (Yii::$app->hasModule($processor)) { // 模块名指定处理类
            $controller = Yii::$app->getModule($processor)->controllerNamespace . '\wechat\ProcessorController';
        } else { // 默认普通控制处理类
            $controller = 'app\controllers\\' . $processor . '\ProcessorController';
        }
        return $controller;
    }

    /**
     * 返回接口控制器路径
     * @param bool $absolute 是否绝对路径
     */
    public static function getProcessorControllerPath($processor, $absolute = false)
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', static::getProcessorController($processor)) . '.php', false);
        return $absolute ? $path : ltrim(str_replace(Yii::getAlias('@app'), '', $path), '/');
    }

    /**
     * Wechat请求匹配
     * @return array|mixed
     */
    protected function match()
    {
        $params = [];
        if ($this->message['msgType'] == 'event') {
            $method = 'matchEvent' . $this->message['event'];
        } else {
            $method = 'match' . $this->message['msgType'];
        }
        if (method_exists($this, $method)) {
            $params = call_user_func([$this, $method]);
        }
        return $params;
    }

    /**
     * 点击菜单拉取消息时的事件推送
     */
    protected function matchEventClick()
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
    protected function matchEventSubscribe()
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
    protected function matchEventScan()
    {
        return [];
    }

    /**
     * 位置事件
     */
    protected function matchEventLocation()
    {
        return [];
    }

    /**
     * 文本消息
     */
    protected function matchText()
    {
        $return = [];
        $models = RuleKeyword::findAllByKeyword($this->message['content'], $this->wechat->model->id);
        if (!empty($models)) {
            foreach ($models as $model) {
                $return[] = [
                    'rule' => $model->rid,
                    'priority' => $model->priority,
                    'keyword' => $model,
                    'wid' => $model->rule->wid,
                    'processor' => $model->processor
                ];
            }
        }
        return $return;
    }

    /**
     * 图片消息
     */
    protected function matchImage()
    {
        return [];
    }

    /**
     * 语音消息
     */
    protected function matchVoice()
    {
        return [];
    }

    /**
     * 视频消息
     */
    protected function matchVideo()
    {
        return [];
    }

    /**
     * 位置消息
     */
    protected function matchLocation()
    {
        return [];
    }

    /**
     * 链接消息
     */
    protected function matchLink()
    {
        return [];
    }
}