<?php 
namespace callmez\wechat\models;

use callmez\wechat\helpers\AccountHelper;
use yii\base\Model;

/**
 * 微信官方后台账户表单
 * @package callmez\wechat\models
 */
class AccountForm extends Model
{
    public $username;
    public $password;
    public $imgCode;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['imgCode', 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '公众平台用户名',
            'password' => '公众平台密码',
            'imgCode' => '登录验证码'
        ];
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        } elseif (($return = AccountHelper::login($this->username, $this->password, $this->imgCode)) !== true) {
            $this->addError('password', '登录失败' . (is_array($return) ? ':' . json_encode($return) : ''));
        }
        return true;
    }

    public function parse()
    {
        return $this->hasErrors() ? false : AccountHelper::getBaseInfo($this->username);
    }
}