<?php
namespace callmez\wechat\components;

use yii\web\Controller;

class WechatController extends Controller
{
    private $_wechats = [];

    /**
     * 存储公众号SDK类
     * @param Wechat $wechat
     */
    public function setWechat(Wechat $wechat)
    {
        $this->_wechats[$wechat->model->id] = $wechat;
    }

    /**
     * 获取指定公众号SDK类
     * @param $id
     * @return bool|object
     */
    public function getWechat($id)
    {
        if (!array_key_exists($id, $this->_wechats)) {
            if ($wechat = Wechat::instanceByCondition($id)) {
                $this->setWechat($wechat);
            } else {
                return false;
            }
        }
        return $this->_wechats[$id];
    }
}