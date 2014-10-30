<?php
namespace callmez\wechat\helpers;

use callmez\storage\helpers\StorageHelper;
use callmez\storage\models\Storage;
use callmez\wechat\models\Wechat;
use Yii;
use Goutte\Client;

class AccountHelper
{
    const WEIXIN_ROOT = 'https://mp.weixin.qq.com';
    const LOGIN_URL = '/cgi-bin/login?lang=zh_CN';
    const CAPTCHA_URL = '/cgi-bin/verifycode';
    const HOME_URL = '/home?t=home/index&lang=zh_CN&token={token}';
    const SETTING_URL = '/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN&token={token}';
    const ADVANCED_URL = '/advanced/advanced?action=dev&t=advanced/dev&token={token}&lang=zh_CN';

    /**
     * 微信后台登录
     * @param $username
     * @param $password
     * @param null $imgCode
     * @return array|bool|float|int|mixed|null|\stdClass|string
     */
    public static function login($username, $password, $imgCode = null)
    {
        $password = md5($password);
        $client = new Client();

        $cacheKey = 'wechat_account_' . $username;
        $auth = Yii::$app->cache->get($cacheKey);
        if (!empty($auth)) { // 已有cookie记录
            $client->getCookieJar()->updateFromSetCookie($auth['cookie']);
            $homeUrl = self::WEIXIN_ROOT . strtr(self::HOME_URL, ['{token}' => $auth['token']]);
            $crawler = self::getCrawler($client, $homeUrl);
            if ($client->getResponse()->getStatus() != 200) {
                return false;
            } elseif (strpos($crawler->html(), '登录超时') !== false) {// 超时删除缓存继续登录获取
                Yii::$app->cache->delete($cacheKey);
            } else {
                return true;
            }
            $client->restart();
        }
        $data = [
            'username' => $username,
            'pwd' => $password,
            'imgcode' => $imgCode,
            'f' => 'json'
        ];
        //登录获取cookie
        $loginUrl = self::WEIXIN_ROOT . self::LOGIN_URL;
        $crawler = $client->request('POST', $loginUrl, $data, [], [
            'HTTP_REFERER' => self::WEIXIN_ROOT
        ]);
        if ($client->getResponse()->getStatus() != 200) {
            return false;
        }
        $content = json_decode($client->getResponse()->getContent()->getContents(), true);
        if (!isset($content['base_resp']['ret']) || $content['base_resp']['ret'] != 0) {
            return $content;
        }
        preg_match('/token=([0-9]+)/', $content['redirect_url'], $match);
        Yii::$app->cache->set($cacheKey, [
            'password' => $password,
            'token' => $match[1],
            'cookie' => $client->getResponse()->getHeader('Set-Cookie', false)
        ]);
        return true;
    }

    /**
     * 获取微信后台基本设置
     * @param $username
     * @return array|bool
     */
    public static function getBaseInfo($username)
    {
        $cacheKey = 'wechat_account_' . $username;
        $auth = Yii::$app->cache->get($cacheKey);
        if (empty($auth)) {
            return false;
        }
        $return = [];

        $client = new Client();
        $client->getCookieJar()->updateFromSetCookie($auth['cookie']);
        $crawler = self::getCrawler($client, self::WEIXIN_ROOT . strtr(self::SETTING_URL, [
            '{token}' => $auth['token']
        ]));
        $domData = ['name', 'avatar', 'email', 'orginal', 'account', 'type', 'type_status', 'primary', 'description', 'address', 'qr_code'];
        $crawler->filter('.meta_content')->each(function($dom, $i) use ($domData, &$return) {
            if ($domData[$i] == 'avatar') {
                $value = self::WEIXIN_ROOT . trim($dom->filter('img')->attr('src'));
            } elseif ($domData[$i] == 'qr_code') {
                $value = self::WEIXIN_ROOT . trim($dom->filter('a img')->attr('src'));
            } else {
                $value = trim($dom->text());
            }
            !empty($value) && $value != '未填写' && $return[$domData[$i]] = $value;
        });

        if (isset($return['type']) && isset($return['type_status'])) { // 公众号类型判断, 赞不能判断企业号
            if ($return['type'] == '订阅号') {
                $return['type'] = $return['type_status'] == '微信认证' ? Wechat::TYPE_SUBSCRIBE_VERIFY : Wechat::TYPE_SUBSCRIBE;
            } elseif ($return['type'] == '服务号') {
                $return['type'] = $return['type_status'] == '微信认证' ? Wechat::TYPE_SERVICE_VERIFY : Wechat::TYPE_SERVICE;
            } else {
                $return['type'] = null;
            }
        }

        $crawler = self::getCrawler($client, self::WEIXIN_ROOT . strtr(self::ADVANCED_URL, [
            '{token}' => $auth['token']
        ]));
        if ($return['type'] == Wechat::TYPE_SUBSCRIBE) {
            $domData = ['api', 'token', 'encoding_aes_key', 'encoding_type'];
        } else {
            $domData = ['app_id', 'app_sceret', 'api', 'token', 'encoding_aes_key', 'encoding_type'];
        }
        $crawler->filter('.frm_controls')->each(function($dom, $i) use ($domData, &$return) {
            $value = trim($dom->html());
            $return[$domData[$i]] = empty($value) || $value == '未填写' ? null : $value;
        });

        if (isset($return['encoding_type'])) { // 消息加密方式
            switch ($return['encoding_type']) {
                case '明文模式':
                    $return['encoding_type'] = Wechat::ENCODING_NORMAL;
                    break;
                case '兼容模式':
                    $return['encoding_type'] = Wechat::ENCODING_COMPATIBLE;
                    break;
                case '安全模式':
                    $return['encoding_type'] = Wechat::ENCODING_SAFE;
                    break;
                default:
                    $return['encoding_type'] = null;
            }
        }

        if (!empty($return['avatar'])) {
            Yii::$app->storageCollection->storage->
        }

        return $return;
    }

    private static function getCrawler($client, $uri)
    {
        //微信官方屏蔽了ssl2和ssl3, 启用更高级的ssl
        $client->getClient()->setDefaultOption('config/curl/' . CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        $client->getClient()->setDefaultOption('verify', false);
        return $client->request('GET', $uri, [], [], [
            'HTTP_REFERER' => self::WEIXIN_ROOT,
        ]);
    }
}