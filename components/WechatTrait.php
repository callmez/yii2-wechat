<?php
namespace callmez\wechat\components;

trait WechatTrait
{
    /**
     * 获取错误信息
     * @return null|string
     */
    public function getLastErrorMessage()
    {
        $message = [];
        if (!empty($this->lastError)) {
            $message = [];
            foreach ($this->lastError as $name => $content) {
                $message[] = $name . ':' . $content;
            }
            $message =  ' [' . implode(',', $message) . ']';
        }
        return $message;
    }
}