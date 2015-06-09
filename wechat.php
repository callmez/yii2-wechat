<?php

/**
 * 自动载入文件.
 * 加载一些常用设置
 *
 * @author CallMeZ https://www.github.com/callmez
 */

// 定义毫秒时间戳
defined('TIMESTAMP') or define('TIMESTAMP', $_SERVER['REQUEST_TIME']);
// 自动注册存储目录(可以在config中配置)
isset(Yii::$aliases['@storage']) or Yii::setAlias('@storage', Yii::getAlias('@web/storage'));
isset(Yii::$aliases['@storageRoot']) or Yii::setAlias('@storageRoot', Yii::getAlias('@webroot/storage'));
