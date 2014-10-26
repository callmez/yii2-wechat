<?php 
namespace callmez\wechat\models;

use yii\base\Model;

/**
 * 微信官方后台登录表单
 * @package callmez\wechat\models
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required']
        ];
    }

}