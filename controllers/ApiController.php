<?php
namespace callmez\wechat\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\web\XmlResponseFormatter;
use yii\web\NotFoundHttpException;
use yii\base\InvalidCallException;
use callmez\wechat\models\Wechat;
use callmez\wechat\components\ProcessEvent;
use callmez\wechat\models\MessageHistory;
use callmez\wechat\models\ReplyRuleKeyword;
use callmez\wechat\components\BaseController;
use callmez\wechat\components\ProcessController;

/**
 * 微信请求处理控制器
 * 该控制器为微信对接接口, 所有微信请求都会通过此控制器来处理请求
 * @package callmez\wechat\controllers
 */
class ApiController extends BaseController
{
    /**
     * 微信请求关闭CSRF验证
     * @var bool
     */
    public $enableCsrfValidation = false;
    /**
     * 微信请求消息
     * @var array
     */
    public $message;

    /**
     * @var Wechat
     */
    private $_wechat;

    /**
     * 设置当前公众号
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
     * 根据ID查找公众号
     * @param $id
     * @return Object
     * @throws NotFoundHttpException
     */
    protected function findWechat($id)
    {
        if (($model = Wechat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 消息处理前事件
     */
    const EVENT_BEFORE_PROCESS = 'beforeProcess';
    /**
     * 消息处理前事件
     * @return mixed
     */
    public function beforeProcess()
    {
        $event = new ProcessEvent($this->message, $this->getWechat());
        $this->trigger(self::EVENT_BEFORE_PROCESS, $event);
        return $event->isValid;
    }

    /**
     * 消息处理后事件
     */
    const EVENT_AFTER_PROCESS = 'afterProcess';
    /**
     * 消息处理后事件
     * @param $result
     * @throws NotFoundHttpException
     */
    public function afterProcess(&$result)
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_PROCESS)) {
            $event = new ProcessEvent($this->message, $this->getWechat());
            $event->result = $result;
            $this->trigger(self::EVENT_AFTER_PROCESS, $event);
            $result = $event->result;
        }
    }

    /**
     * 微信请求响应Action
     * 分析请求后分发给指定的处理流程
     * @param $id
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public function actionIndex($id)
    {
        // TODO 群发事件推送群发处理
        // TODO 模板消息事件推送处理
        // TODO 用户上报地理位置事件推送处理
        // TODO 自定义菜单事件推送处理
        // TODO 微信小店订单付款通知处理
        // TODO 微信卡卷(卡券通过审核、卡券被用户领取、卡券被用户删除)通知处理
        // TODO 智能设备接口
        // TODO 多客服转发处理
        $request = Yii::$app->request;
        $wechat = $this->findWechat($id);
        if (!$wechat->getSdk()->checkSignature()) {
            return 'Sign check fail!';
        }
        switch ($request->getMethod()) {
            case 'GET':
                if ($wechat->status == Wechat::STATUS_INACTIVE) { // 激活公众号
                    $wechat->updateAttributes(['status' => Wechat::STATUS_ACTIVE]);
                }
                return $request->get('echostr');
            case 'POST':
                $this->setWechat($wechat);
                $this->message = $this->parseRequest();
                $result = null;
                if($this->beforeProcess()) {
                    $result = $this->resolveProcess(); // 处理请求
                    $this->afterProcess($result);
                }
                return is_array($result) ? $this->createResponse($result) : $result;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 解析微信请求内容
     * @return array
     * @throws NotFoundHttpException
     */
    public function parseRequest()
    {
        $xml = $this->getWechat()->getSdk()->parseRequestXml();
        if (empty($xml)) {
            Yii::$app->response->content = 'Request data parse failed!';
            Yii::$app->end();
        }
        $message = [];
        foreach ($xml as $attribute => $value) {
            $message[$attribute] = is_array($value) ? $value : (string) $value;
        }

        Yii::info($message, __METHOD__);
        return $message;
    }

    /**
     * 生成响应内容Response
     * @param array $data
     * @return object
     */
    public function createResponse(array $data)
    {
        $timestamp = time();
        $data = array_merge([
            'FromUserName' => $this->message['ToUserName'],
            'ToUserName' => $this->message['FromUserName'],
            'CreateTime' => $timestamp
        ], $data);

        Yii::info($data, __METHOD__);

        $sdk = $this->getWechat()->getSdk();
        $xml = $sdk->xml($data);
        if ($xml && Yii::$app->request->get('encrypt_type') == 'aes') { // aes加密
            $xml = $sdk->encryptXml($xml, $timestamp, Yii::$app->security->generateRandomString(6));
        }
        return $xml;
    }

    /**
     * 解析到控制器
     * @return null
     */
    public function resolveProcess()
    {
        $result = null;
        foreach ($this->match() as $model) {
            if ($model instanceof ReplyRuleKeyword) {
                $processor = $model->rule->processor;
                $route = $processor[0] == '/' ? $processor : '/wechat/' . $model->rule->mid . '/' . $model->rule->processor;
            } elseif (isset($model['route'])) { // 直接返回处理route
                $route = $model['route'];
            } else {
                continue;
            }

            // 转发路由请求 参考: Yii::$app->runAction()
            $parts = Yii::$app->createController($route);
            if (is_array($parts)) {
                list($controller, $actionID) = $parts;

                // 微信请求的处理器必须继承callmez\wechat\components\ProcessController
                if (!($controller instanceof ProcessController)) {
                    throw new InvalidCallException("Wechat process controller must instance of '" . ProcessController::className() . "'");
                }
                // 传入当前公众号和微信请求内容
                $controller->message = $this->message;
                $controller->setWechat($this->getWechat());

                $oldController = Yii::$app->controller;
                $result = $controller->runAction($actionID);
                Yii::$app->controller = $oldController;
            }

            // 如果有数据则跳出循环直接输出. 否则只作为订阅类型继续循环处理
            if ($result !== null) {
                break;
            }
        }


        $module = isset($controller) ? $controller->module->id : 'wechat'; // 处理的模块
        if ($model instanceof ReplyRuleKeyword) {
            $kid = $model->id;
            $rid = $model->rid;
        } else {
            $kid = $rid = 0;
        }
        // 记录请求内容
        MessageHistory::add([
            'wid' => $this->getWechat()->id,
            'rid' => $rid,
            'kid' => $kid,
            'from' => $this->message['FromUserName'],
            'to' => $this->message['ToUserName'],
            'module' => $module,
            'message' => $this->message,
            'type' => MessageHistory::TYPE_REQUEST
        ]);
        // 记录响应内容
        if ($result !== null) {
            // 记录响应内容
            MessageHistory::add([
                'wid' => $this->getWechat()->id,
                'rid' => $rid,
                'kid' => $kid,
                'from' => $this->message['ToUserName'],
                'to' => $this->message['FromUserName'],
                'module' => $module,
                'message' => $result,
                'type' => MessageHistory::TYPE_RESPONSE
            ]);
        }

        return $result;
    }

    /**
     * 回复规则匹配
     * @return array|mixed
     */
    public function match()
    {
        if ($this->message['MsgType'] == 'event') { // 事件
            $method = 'matchEvent' . $this->message['Event'];
        } else {
            $method = 'match' . $this->message['MsgType'];
        }
        $matches = [];
        if (method_exists($this, $method)) {
            $matches = call_user_func([$this, $method]);
        }
        $matches = array_merge([
            ['route' => '/wechat/process/fans/record'] // 记录常用数据
        ], $matches);

        Yii::info($matches, __METHOD__);
        return $matches;
    }

    /**
     * 文本消息关键字触发
     * @return array
     */
    protected function matchText()
    {
        return ReplyRuleKeyword::find()
            ->keyword($this->message['Content'])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 图片消息触发
     * @return mixed
     */
    protected function matchImage()
    {
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => ReplyRuleKeyword::TYPE_IMAGE])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 音频消息触发
     * @return mixed
     */
    protected function matchVoice()
    {
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => ReplyRuleKeyword::TYPE_VOICE])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 视频, 短视频消息触发
     * @return mixed
     */
    protected function matchVideo()
    {
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => [ReplyRuleKeyword::TYPE_VIDEO, ReplyRuleKeyword::TYPE_SHORT_VIDEO]])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 位置消息触发
     * @return mixed
     */
    protected function matchLocation()
    {
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => [ReplyRuleKeyword::TYPE_LOCATION]])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 链接消息触发
     * @return mixed
     */
    protected function matchLink()
    {
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => [ReplyRuleKeyword::TYPE_LINK]])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 关注事件
     * @return array|void
     */
    protected function matchEventSubscribe()
    {
        // 扫码关注
        if (array_key_exists('Eventkey', $this->message) && strexists($this->message['Eventkey'], 'qrscene')) {
            $this->message['Eventkey'] = explode('_', $this->message['Eventkey'])[1]; // 取二维码的参数值
            return $this->matchEventScan();
        }
        // 订阅请求回复规则触发
        return ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => [ReplyRuleKeyword::TYPE_SUBSCRIBE]])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
    }

    /**
     * 取消关注事件
     * @return array
     */
    protected function matchEventUnsubscribe()
    {
        $match = ReplyRuleKeyword::find()
            ->andFilterWhere(['type' => [ReplyRuleKeyword::TYPE_UNSUBSCRIBE]])
            ->wechatRule($this->getWechat()->id)
            ->limitTime(TIMESTAMP)
            ->all();
        return array_merge([ // 取消关注默认处理
            ['route' => '/wechat/process/fans/unsubscribe']
        ], $match);
    }

    /**
     * 用户已关注时的扫码事件触发
     * @return array
     */
    protected function matchEventScan()
    {
        if (array_key_exists('Eventkey', $this->message)) {
            $this->message['Content'] = $this->message['EventKey'];
            return $this->matchText();
        }
        return [];
    }

    /**
     * 上报地理位置事件触发
     * @return mixed
     */
    protected function matchEventLocation()
    {
        return $this->matchLocation(); // 直接匹配位置消息
    }

    /**
     * 点击菜单拉取消息时的事件触发
     * @return array
     */
    protected function matchEventClick()
    {
        // 触发作为关键字处理
        if (array_key_exists('EventKey', $this->message)) {
            $this->message['Content'] = $this->message['EventKey']; // EventKey作为关键字Content
            return $this->matchText();
        }
        return [];
    }

    /**
     * 点击菜单跳转链接时的事件触发
     * @return array
     */
    protected function matchEventView()
    {
        // 链接内容作为关键字
        if (array_key_exists('EventKey', $this->message)) {
            $this->message['Content'] = $this->message['EventKey']; // EventKey作为关键字Content
            return $this->matchText();
        }
        return [];
    }
}
