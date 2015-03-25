<?php
namespace callmez\wechat\widgets;

use yii\helpers\Html;

class ActiveField extends \yii\bootstrap\ActiveField
{
//    public $fileTextInputTemplate = "{label}\n{beginWrapper}\n<div class=\"input-group\">\n{input}\n<span class=\"input-group-addon file-input\">\n{fileInput}\n</span>\n</div>\n{error}\n{endWrapper}\n{hint}";
//
//    public function fileTextInput($options = [], $fileInput = null)
//    {
//        if (!isset($options['template'])) {
//            $this->template = $this->fileTextInputTemplate;
//        } else {
//            $this->template = $options['template'];
//            unset($options['template']);
//        }
//        if (!isset($options['fileInput'])) {
//            $this->parts['{fileInput}'] = Html::activeFileInput($this->model, $this->attribute) . '点击上传';
//        } else {
//            $this->template = $options['fileInput'];
//            unset($options['fileInput']);
//        }
//        return $this->textInput($options);
//    }
}