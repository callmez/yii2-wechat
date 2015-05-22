<?php
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";
?>
namespace <?= $generator->getModuleNamespace() ?>\controllers;

/**
 * 微信请求默认处理类.
 * 注: 处理类必须继承ProcessController基类以使用微信消息的相关功能
 */
class ProcessController extends \callmez\wechat\components\ProcessController
{
    /**
     * 解析到该Action需要添加回复规则, 可以在后台增加回复规则控制菜单来管理
     */
    public function actionIndex()
    {
        // 微信请求的信息主体内容
        // $message = $this->message;

        // 返回文本消息,返回其他类型信息和微信消息相关处理功能请查看继承的父类
        // responseImage, responseNews, responseVideo, responseVoice, response, getWechat
        //return $this->responseText('回复文字:哈哈');
    }
}
