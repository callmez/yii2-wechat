<?php
namespace callmez\wechat\widgets;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    /**
     * @inherit
     */
    public $fieldClass = 'callmez\wechat\widgets\ActiveField';
}