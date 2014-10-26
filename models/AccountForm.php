<?php 
namespace callmez\wechat\models;

use yii\base\Model;

/**
 * 微信官方后台账户表单
 * @package callmez\wechat\models
 */
class AccountForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '公众平台用户名',
            'password' => '公众平台密码'
        ];
    }
}