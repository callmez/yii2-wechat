<?php
namespace callmez\wechat\widgets;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    /**
     * @inheritdoc
     */
    public $fieldClass = 'callmez\wechat\widgets\ActiveField';
}