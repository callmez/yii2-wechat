<?php
namespace callmez\wechat\controllers;

use Yii;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\components\ProcessEvent;
use callmez\wechat\components\BaseController;
use app\modules\test\controllers\ProcessController;

class ApiController extends BaseController
{
    const EVENT_BEFORE_PROCESS = 'beforeProcess';
    const EVENT_AFTER_PROCESS = 'afterProcess';

    /**
     * 微信请求关闭CSRF验证
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @var Object
     */
    public $message;

    /**
     * 微信请求响应Action
     * 分析请求后分发给指定的处理流程
     * @param $hash
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public function actionIndex($hash)
    {
        $request = Yii::$app->getRequest();
        switch ($request->getMethod()) {
            case 'GET':
                return $request->getQueryParam('echostr');
            case 'POST':
                $this->setWechat($this->findWechat($hash));
                $this->message = $this->parseRequest($request->getRawBody());
                $result = null;
                if($this->beforeProcess()) {
                    $result = $this->resolveProcess(); // 处理请求
                    $result = $this->afterProcess($result);
                }
                return $result;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');

        }
    }

    private $_wechat;

    /**
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        $this->_wechat = $wechat;
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function getWechat()
    {
        if ($this->_wechat === null) {
            throw new NotFoundHttpException('The "wechat" property must be set.');
        }
        return $this->_wechat;
    }

    /**
     * 根据微信唯一hash值查找微信公众号
     * @param $hash
     * @return Object
     * @throws NotFoundHttpException
     */
    protected function findWechat($hash)
    {
        if (($model = Wechat::findOne(['hash' => $hash])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 解析微信发送的请求内容
     * @param $message
     * @return \stdClass
     * @throws NotFoundHttpException
     */
    protected function parseRequest($message)
    {
        $xml = $this->getWechat()->getSdk()->parseRequestData($message);
        if (empty($xml)) {
            Yii::$app->response->content = 'Request Failed!';
            Yii::$app->end();
        }
        $return = new \stdClass();
        foreach ($xml as $attribute => $value) {
            $attribute = lcfirst($attribute);
            $return->$attribute = (string)$value;
        }
        Yii::info($return, __METHOD__);
        return $return;
    }

    /**
     * 微信请求处理前出发事件
     * @return bool
     * @throws NotFoundHttpException
     */
    public function beforeProcess()
    {
        $event = new ProcessEvent($this->message, $this->getWechat());
        $this->trigger(self::EVENT_BEFORE_PROCESS, $event);
        return $event->isValid;
    }

    /**
     * 微信请求处理完后触发事件
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function afterProcess($result)
    {
        $event = new ProcessEvent($this->message, $this->getWechat());
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_PROCESS, $event);
        return $event->result;
    }

    /**
     * 默认处理程序路由
     * @var string
     */
    public $defaultProcessRoute = 'process';

    /**
     * 根据微信的请求信息分发到指定处理流程
     * @return int|mixed|null
     * @throws InvalidRouteException
     */
    public function resolveProcess()
    {
        $result = null;
        foreach ($this->match() as $params) {
            !isset($params['mdoule']) && $params['mdoule'] = $this->module->id;
            !isset($params['process']) && $params['process'] = $this->defaultProcessRoute;
            $route = implode('/', [$params['module'], $params['process']]);
            unset($params['module'], $params['process']);
            if (($result = Yii::$app->runAction($route, $params)) !== null) { // 直接转发路由请求
                break; // 如果有数据则跳出循环直接输出.
            }
        }
        return $result;
    }

    /**
     * 微信请求类型处理
     * 返回格式
     * [
     *     [
     *         'module' => '处理模块' // 必须返回
     *     ]
     * ]
     * @return array|mixed
     */
    public function match()
    {
        $method = 'match' . ($this->message->msgType == 'event' ? 'Event' . $this->message->event : $this->message->msgType);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        return [];
    }

    /**
     * 点击事件处理
     * @return array
     */
    protected function matchEventClick()
    {
        // 触发作为关键字处理
        if (property_exists($this->message, 'eventKey') && $this->message->content = $this->message->eventKey) {
            return $this->matchText();
        }
        return [];
    }

    protected function matchEventSubscribe()
    {
        
    }

    protected function matchEventScan()
    {
        
    }

    protected function matchEventLocation()
    {

    }

    /**
     * 关键字触发
     * @return array
     * @throws NotFoundHttpException
     */
    protected function matchText()
    {
        $params = [];
        $models = RuleKeyword::findAllByKeyword($this->message->content, $this->getWechat()->id);
        foreach ($models as $model) {
            $params[] = [
                'module' => $model->rule->module
            ];
        }
        return $params;
    }

    protected function matchImage()
    {
        
    }

    protected function matchVoice()
    {
        
    }

    protected function matchVideo()
    {

    }

    protected function matchLocation()
    {
        
    }

    protected function matchLink()
    {
        
    }
}
