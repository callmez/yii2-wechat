<?php
namespace callmez\wechat\components;

use callmez\wechat\models\Module;

/**
 * 扩展模块基类,所有扩展模块都必须继承该类
 * @package callmez\wechat\components\BaseModule
 */
abstract class BaseModule extends \yii\base\Module
{
    /**
     * 默认后台路由
     * @var string
     */
    public $defaultAdminRoute = 'admin';

    private $_adminHomeUrl;

    /**
     * 获取后台首页Url
     * @return null
     */
    public function getAdminHomeUrl()
    {
        if ($this->_adminHomeUrl === null) {
            $this->setAdminHomeUrl(['/wechat/' . $this->id . '/' . $this->defaultAdminRoute]);
        }
        return $this->_adminHomeUrl;
    }

    /**
     * 设置后台首页Url
     * @param $url
     */
    public function setAdminHomeUrl($url)
    {
        $this->_adminHomeUrl = $url;
    }

    /**
     * 是否有后台菜单
     * @return bool
     */
    public function hasAdminMenus()
    {
        return $this->getAdminMenus() !== [];
    }

    private $_model = false;

    /**
     * 获取扩展模块Model
     * @return Module
     */
    public function getModel()
    {
        if ($this->_model === false) {
            $class = $this->module->moduleModelClass;
            $this->setModel($class::findOne(['id' => $this->id]));
        }
        return $this->_model;
    }

    /**
     * 设置扩展模块Model
     * @param Module $model
     */
    public function setModel(Module $model)
    {
        $this->_model = $model;
    }

    /**
     * 获取后台菜单
     * @return array
     */
    public function getAdminMenus()
    {
        $menus = [];
        $class = $this->module->menuModuleClass;
        return $menus;
    }
}