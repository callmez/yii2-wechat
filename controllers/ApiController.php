<?php
namespace callmez\wechat\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\components\ProcessEvent;
use callmez\wechat\components\BaseController;

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
     * 处理相关请求的模块.
     * 例:
     * [
     *     'subscribe' => 'fans' // 需模块存在 否则依然走默认模块
     * ]
     * @var array
     */
    public $moduleMap = [];
    /**
     * @var array
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
        $return = [];
        foreach ($xml as $attribute => $value) {
            $attribute = lcfirst($attribute);
            $return[$attribute] = (string)$value;
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

    public $defaultProcess = 'process';
    /**
     * 根据微信的请求信息分发到指定处理流程
     * @return int|mixed|null
     * @throws InvalidRouteException
     */
    public function resolveProcess()
    {
        $result = null;
        foreach ($this->match() as $params) {
            $receive = ArrayHelper::remove($params, 'receive');
            if ($receive || $process = ArrayHelper::remove($params, 'process', $this->defaultProcess)) {
                $route = implode('/', [ArrayHelper::remove($params, 'module', $this->module->id), $receive ?: $process]);
                $result = Yii::$app->runAction($route, $params); // 直接转发路由请求
                // 订阅器则继续循环, 如果有数据则跳出循环直接输出.
                if (!$receive && $result !== null) {
                    break;
                }
            }
        }
        return $result;
    }

    public $defaultMatch = [
        [
            'receive' => 'fans/record' // 记录用户请求
        ]
    ];

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
        $params = $this->defaultMatch;
        $method = 'match' . ($this->message['msgType'] == 'event' ? 'Event' . $this->message['event'] : $this->message['msgType']);
        if (method_exists($this, $method)) {
            $params = array_merge($params, call_user_func([$this, $method]));
        }
        ArrayHelper::multisort($params, [function ($item) {
           return isset($item['receive']);
        }, 'priority'], SORT_DESC);// 按订阅器(优先)和权重(次计算)倒序排序, 订阅器优先按权重处理
        return $params;
    }

    /**
     * 点击事件处理
     * @return array
     */
    protected function matchEventClick()
    {
        // 触发作为关键字处理
        if (array_key_exists($this->message, 'eventKey') && $this->message['content'] = $this->message['eventKey']) {
            return $this->matchText();
        }
        return [];
    }

    /**
     * 关注事件
     * @return array|void
     */
    protected function matchEventSubscribe()
    {
        $params = [];
        if (array_key_exists($this->message, 'eventkey') && strexists($this->message['eventkey'], 'qrscene')) { // 扫码关注
            list(, $this->message['eventkey']) = explode('_', $this->message['eventkey']); // 取二维码的参数值
            return $this->matchEventScan();
        } elseif (isset($this->moduleMap['subscribe']) && Yii::$app->hasModule($this->moduleMap['subscribe'])) {
            $params[] = [ // 关注操作, 默认的操作已经由FansController::actionRecord自动处理. 这里只需处理关注记录完成后的相关处理
                'module' => $this->moduleMap['subscribe'],
                'process' => 'subscribe'
            ];
        } else { // 默认由WecomeController类处理
            $params[] = [
                'module' => $this->module->id,
                'process' => 'welcome'
            ];
        }
        return $params;
    }

    /**
     * 取消关注事件
     * 注:取消关注时间已经合并到FansController::actionRecord中处理
     * @return array
     */
    //protected function matchEventUnsubscribe()
    //{}

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
        $models = RuleKeyword::findAllByKeyword($this->message['content'], $this->getWechat()->id);
        foreach ($models as $model) {
            $params[] = [
                'module' => $model->rule->module,
                'priority' => $model->priority
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
