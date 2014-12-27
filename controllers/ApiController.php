<?php
namespace callmez\wechat\controllers;

use callmez\wechat\models\Module;
use Yii;
use yii\web\Controller;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use callmez\wechat\components\Wechat;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\components\ProcessorEvent;
use callmez\wechat\components\WechatProcessorController;

class ApiController extends Controller
{
    const EVENT_BEFORE_PROCESSOR = 'beforeProcessor';
    const EVENT_AFTER_PROCESSOR = 'afterProcessor';
    /**
     * 微信服务器请求内容
     * @var object
     */
    public $message;
    /**
     * 微信SDK类
     * @var object
     */
    public $wechat;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'receiver';
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * 微信请求接收器
     * @param $hash
     * @return array
     */
    public function actionReceiver($hash)
    {
        $request = Yii::$app->request;
        switch ($request->method) {
            case 'GET':
                Yii::$app->response->content = $request->getQueryParam('echostr');
                return Yii::$app->end();
            case 'POST':
                $this->wechat = $this->findWechat(); // 查找公众号
                $this->message = $this->parseRequest(); // 解析请求信息

                $this->attachReceiverEvent(); // 注册 模块订阅事件

                $result = null;
                if($this->beforeProcessor()) {
                    $result = $this->resolveProcessor(); // 处理请求
                    $result = $this->afterProcessor($result);
                }
                return $result;
            default:
                return null;
        }
    }

    /**
     * 消息处理前事件
     * @return bool
     */
    public function beforeProcessor()
    {
        $event = new ProcessorEvent($this->message, $this->wechat);
        $this->trigger(self::EVENT_BEFORE_PROCESSOR, $event);
        return $event->isValid;
    }

    /**
     * 消息处理后事件
     * @param $result
     * @return mixed
     */
    public function afterProcessor($result)
    {
        $event = new ProcessorEvent($this->message, $this->wechat);
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_PROCESSOR, $event);
        return $event->result;
    }

    /**
     * 查找微信公众号
     * @param array $condition
     * @return bool|object
     */
    public function findWechat(array $condition = [])
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
    public function parseRequest($message = null)
    {
        $return = new \stdClass();
        if (($xml = $this->wechat->parseRequestData($message)) !== []) {
            foreach($xml as $k => $v) {
                if (in_array($k, ['FromUserName', 'ToUserName'])) { //转换成 from, to 键值
                    $k = str_replace('UserName', '', $k);
                }
                $k[0] = strtolower($k[0]);
                $return->$k = strval($v);
            }
        } else {
            Yii::$app->response->content = 'Request Failed!';
            Yii::$app->end();
        }
        Yii::info($this->message, __METHOD__);
        return $return;
    }

    /**
     * 微信扩展模块的订阅接收器 事件注册
     */
    public function attachReceiverEvent()
    {
        foreach (Module::getInstallerModules() as $k => $module)
        {
            if (in_array('receiver', $module->services)) {

            }
        }
    }

    /**
     * 消息处理器转发
     * @return mixed
     */
    public function resolveProcessor()
    {
        $result = null;
        foreach ($this->match() as $param) {
            if ($this->module->hasModule($param['module'])) {
                $route = implode('/', [$param['module'], 'processor']);

                $parts = $this->module->createController($route);
                if (is_array($parts)) {
                    /* @var $controller Controller */
                    list($controller, $actionID) = $parts;

                    if (!($controller instanceof WechatProcessorController)) {
                        throw new InvalidConfigException("The processor controller must be instance of '" . WechatProcessorController::className() . "'");
                    }
                    $controller->message = $this->message;
                    $controller->wechat = $this->wechat;

                    $oldController = Yii::$app->controller;
                    Yii::$app->controller = $controller;
                    $result = $controller->runAction($actionID, Yii::$app->requestedParams);
                    Yii::$app->controller = $oldController;

                    if ($result !== null) {
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Wechat请求匹配
     * @return array|mixed
     */
    public function match()
    {
        $params = [];
        if ($this->message->msgType == 'event') {
            $method = 'matchEvent' . $this->message->event;
        } else {
            $method = 'match' . $this->message->msgType;
        }
        if (method_exists($this, $method)) {
            $params = call_user_func([$this, $method]);
        }
        return $params;
    }

    /**
     * 点击菜单拉取消息时的事件推送
     */
    public function matchEventClick()
    {
        if (property_exists($this->message, 'eventKey') && $this->message->content = $this->message->eventKey) {
            return $this->matchText(); // 作为关键字处理
        }
        return [];
    }

    /**
     * 扫描带参数二维码事件
     */
    public function matchEventSubscribe()
    {
        $params = [];
        //扫描带参数二维码事件
        if (property_exists($this->message, 'eventKey') && strpos($this->message->eventKey, 'qrscene') !== false) {
            list(, $this->message->eventkey) = explode('_', $this->message->eventKey);
            $params = $this->matchEventScan(); // 扫描事件处理
        }
        return $params;
    }

    /**
     * 扫码事件
     */
    public function matchEventScan()
    {
        return [];
    }

    /**
     * 位置事件
     */
    public function matchEventLocation()
    {
        return [];
    }

    /**
     * 文本消息
     */
    public function matchText()
    {
        $return = [];
        $models = RuleKeyword::findAllByKeyword($this->message->content, $this->wechat->model->id);
        foreach ($models as $model) {
            $return[] = [
                'module' => $model->rule->module
            ];
        }
        return $return;
    }

    /**
     * 图片消息
     */
    public function matchImage()
    {
        return [];
    }

    /**
     * 语音消息
     */
    public function matchVoice()
    {
        return [];
    }

    /**
     * 视频消息
     */
    public function matchVideo()
    {
        return [];
    }

    /**
     * 位置消息
     */
    public function matchLocation()
    {
        return [];
    }

    /**
     * 链接消息
     */
    public function matchLink()
    {
        return [];
    }
}
