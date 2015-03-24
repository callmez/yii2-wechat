<?php
namespace callmez\wechat\widgets;

class ActiveField extends \yii\bootstrap\ActiveField
{

    /**
     * 上传图片和文本框组成input-group
     * @param $fileInput
     * @param null $template
     * @return $this
     */
    public function fileTextGroup($fileInput, $template = null)
    {
        if ($template === null) {
            $this->template = "{label}\n{beginWrapper}\n<div class=\"input-group\"><span class=\"input-group-addon file-input\">\n{fileInput}\n</span>\n{input}</div>\n{error}\n{endWrapper}\n{hint}";
        }
        $this->parts['{fileInput}'] = $fileInput;

        return $this;
    }
}