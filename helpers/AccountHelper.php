<?php
namespace callmez\wechat\sdk\Wechat;

class AccountHelper
{
    public static $loginUrl = '/cgi-bin/login?lang=zh_CN';
    public static function login($username, $password, $imgCode = '')
    {
        $data = [
            'username' => $username,
            'pwd' => $password,
            'imgcode' => $imgCode,
            'f' => 'json'
        ];
    }
}