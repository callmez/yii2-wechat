<?php
namespace callmez\wechat\widgets;

/**
 * 微信管理后台自定义GridView
 * @package callmez\wechat\widgets
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @inheritdoc
     */
    public $tableOptions = ['class' => 'table table-hover'];
}