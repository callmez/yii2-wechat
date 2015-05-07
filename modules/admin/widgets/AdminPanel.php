<?php
namespace callmez\wechat\modules\admin\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

class AdminPanel extends Widget
{
    public $title;

    public $options = [];

    public function init()
    {
        if ($this->title === null && ($this->title = $this->getView()->title) === null) {
            throw new InvalidConfigException('The "title" property must be set.');
        }
        echo Html::beginTag('div', $this->options);
        echo Html::beginTag('div', [
            'class' => 'panel panel-default'
        ]);
        echo Html::tag('div', '<b>' . Html::encode($this->title) . '</b>', [
            'class' => 'panel-heading'
        ]);
        echo Html::beginTag('div', [
            'class' => 'panel-body'
        ]);
    }

    public function run()
    {
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
}