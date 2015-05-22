<?php
namespace callmez\wechat\components;

use yii\base\Module;
use callmez\wechat\models\Module as ModuleModel;

/**
 * 微信扩展模块基类
 * 所有微信的扩展模块必须继承此类
 *
 * @package callmez\wechat\components
 */
class BaseModule extends Module
{

    /**
     * @var array
     */
    private $_adminMenus;

    /**
     * 获取后台菜单
     * @return mixed|null
     */
    public function getAdminMenus()
    {
        if ($this->_adminMenus === null) {
            $this->setAdminMenus(array_merge($this->defaultAdminMenus(), $this->adminMenus()));
        }
        return $this->_adminMenus;
    }

    /**
     * 设置后台菜单
     * @param $menus
     */
    public function setAdminMenus($menus)
    {
        return $this->_adminMenus = $menus;
    }

    /**
     * @var ModuleModel
     */
    private $_moduleModel;

    /**
     * 获取模块Model
     * @return mixed|null
     */
    public function getModuleModel()
    {
        if ($this->_moduleModel === null) {
            // 默认根据缓存的模块数据生成模块model
            $class = $this->module->moduleModelClass;
            $model = new $class;
            $class::populateRecord($model, ModuleModel::models()[$this->id]);
            $this->setModuleModel($model);
        }
        return $this->_moduleModel;
    }

    /**
     * 设置模块模型
     * @param $model
     * @return mixed
     */
    public function setModuleModel($model)
    {
        return $this->_moduleModel = $model;
    }

    /**
     * 后台默认菜单
     * 返回一些模块预设定的菜单设置
     * 你可以通过adminMenus()来覆盖修改该设置
     * @return array
     */
    protected function defaultAdminMenus()
    {
        $menus = [];
        if ($this->getModuleModel()->reply_rule) { // 开启回复规则
            $menus['reply'] = [
                'label' => '回复规则',
                'url' => ['/wechat/reply', 'mid' => $this->id]
            ];
        }
        return $menus;
    }

    /**
     * 后台菜单生成
     * @return array
     */
    protected function adminMenus()
    {
        return [];
    }
}