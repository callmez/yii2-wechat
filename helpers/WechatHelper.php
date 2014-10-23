<?php
namespace callmez\wechat\helpers;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidParamException;

class WechatHelper
{
    public static function getApiLink(array $data)
    {
        if (!isset($data['token'])) {
            throw new InvalidParamException('The token property must be set.');
        } else {
            $token = $data['token'];
            unset($data['token']);
        }
        $nonce = Yii::$app->security->generateRandomString(5);
        $timestamp = $_SERVER['REQUEST_TIME'];
        $signArray = [$token, $timestamp, $nonce];
        sort($signArray, SORT_STRING);
        $signature = sha1(implode($signArray));

        return Url::toRoute(array_merge([
            '/wechat/api',
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signature' => $signature
        ], $data));
    }

}