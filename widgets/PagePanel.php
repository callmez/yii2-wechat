<?php
namespace callmez\wechat\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

/**
 * 页面Panel布局
 * @package callmez\wechat\widgets
 */
class PagePanel extends Widget
{
    /**
     * 标题
     * @var string
     */
    public $title;
    /**
     * 标题附加内容
     * 该内容并未Html::encode()安全过滤, 需自行过滤
     * @var string
     */
    public $rightHtml = '';
    /**
     * @var array
     */
    public $options = [];

    public function init()
    {
        if ($this->title === null && ($this->title = $this->getView()->title) === null) {
            throw new InvalidConfigException('The page "title" property must be set.');
        }
        echo Html::beginTag('div', $this->options);
        echo Html::beginTag('div', [
            'class' => 'panel panel-default'
        ]);
        echo Html::tag('div', '<b>' . Html::encode($this->title) . '</b>' . $this->rightHtml, [
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