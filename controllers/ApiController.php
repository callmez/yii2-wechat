<?php
namespace callmez\wechat\controllers;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\ReplyRuleKeyword;
use callmez\wechat\components\BaseController;

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
     * 微信请求响应Action
     * 分析请求后分发给指定的处理流程
     * @param $hash
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public function actionIndex($hash)
    {
        $request = Yii::$app->getRequest();
        $wechat = $this->findWechat($hash);
        if (!$wechat->getSdk()->checkSignature()) {
            return 'Sign check fail!';
        }
        switch ($request->getMethod()) {
            case 'GET':
                if ($wechat->status == Wechat::STATUS_INACTIVE) { // 激活公众号
                    $wechat->updateAttributes(['status' => Wechat::STATUS_ACTIVE]);
                }
                return $request->getQueryParam('echostr');
            case 'POST':
                $this->setWechat($wechat);
                $this->message = $this->parseRequest($request->getRawBody());
                $result = $this->resolveProcess(); // 处理请求
                return is_array($result) ? $this->createResponse($result) : $result;
            default:
                throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

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
     * 解析微信请求内容
     * @param $rawMessage
     * @throws NotFoundHttpException
     */
    public function parseRequest($rawMessage)
    {
        $xml = $this->getWechat()->getSdk()->parseRequestData($rawMessage);
        if (empty($xml)) {
            Yii::$app->response->content = 'Request Failed!';
            Yii::$app->end();
        }
        $message = [];
        foreach ($xml as $attribute => $value) {
            $message[$attribute] = is_array($value) ? $value : (string) $value;
        }
        Yii::info($message, __METHOD__);
        return $message;
    }

    public function resolveProcess()
    {
        $result = null;
        foreach ($this->match() as $model) {
            $processor = $model->rule->processor;
            $route = $processor[0] == '/' ? $processor : '/wechat/' . $model->rule->mid . '/' . $model->rule->processor;

            // 转发路由请求 see Yii::$app->runAction()
            $parts = Yii::$app->createController($route);
            if (is_array($parts)) {
                list($controller, $actionID) = $parts;
                $oldController = Yii::$app->controller;
                $result = $controller->runAction($actionID);
                Yii::$app->controller = $oldController;
            }

            // 如果有数据则跳出循环直接输出. 否则只作为订阅内容处理继续循环
            if ($result !== null) {
                break;
            }
        }
        return $result;
    }

    /**
     * 生成响应内容Response
     * @param array $data
     * @return object
     */
    public function createResponse(array $data)
    {
        Yii::info($data, __METHOD__);

        $response = Yii::createObject([
            'class' => Response::className(),
            'format' => Response::FORMAT_XML,
            'data' => $data
        ]);
        $response->formatters[Response::FORMAT_XML] = [
            'class' => $response->formatters[Response::FORMAT_XML],
            'rootTag' => 'xml',
            'contentType' => 'text/html' // 输出html为兼容性考虑
        ];
        return $response;
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
        $params = [];
        if (method_exists($this, $method)) {
            $params = call_user_func([$this, $method]);
        }
        return $params;
    }

    /**
     * 文本消息关键字触发
     * @return array
     */
    protected function matchText()
    {
        return ReplyRuleKeyword::find()
            ->keyword($this->message['Content'])
            ->wechat($this->getWechat()->id)
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
            ->wechat($this->getWechat()->id)
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
            ->wechat($this->getWechat()->id)
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
            ->wechat($this->getWechat()->id)
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
            ->wechat($this->getWechat()->id)
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
            ->wechat($this->getWechat()->id)
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
        if (array_key_exists($this->message, 'Eventkey') && strexists($this->message['Eventkey'], 'qrscene')) {
            $this->message['Eventkey'] = explode('_', $this->message['Eventkey'])[1]; // 取二维码的参数值
            return $this->matchEventScan();
        }
        // TODO 关注操作
        return [];
    }

    /**
     * 取消关注事件
     * @return array
     */
    protected function matchEventUnsubscribe()
    {
        // TODO 取消关注操作
        return [];
    }

    /**
     * 用户已关注时的扫码事件触发
     * @return array
     */
    protected function matchEventScan()
    {
        if (array_key_exists($this->message, 'Eventkey')) {
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
        if (array_key_exists($this->message, 'EventKey')) {
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
        if (array_key_exists($this->message, 'EventKey')) {
            $this->message['Content'] = $this->message['EventKey']; // EventKey作为关键字Content
            return $this->matchText();
        }
        return [];
    }
}
