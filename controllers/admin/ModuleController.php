<?php

namespace callmez\wechat\controllers\admin;

use Yii;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Module;
use callmez\wechat\components\ModuleInstaller;
use callmez\wechat\components\ModuleDiscovery;
use callmez\wechat\components\WechatAdminController;

/**
 * ModuleController implements the CRUD actions for Module model.
 */
class ModuleController extends WechatAdminController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 模块列表
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->moduleList();
    }

    /**
     * 卸载模块
     * @return mixed
     */
    public function actionUninstall()
    {
        return $this->moduleList(true);
    }

    protected function moduleList($uninstall = false)
    {
        $availableModules = $this->module->getAvailableModules();
        $installedModules = $this->module->getInstalledModules();
        $dataProvider = new ArrayDataProvider([
            'models' => $uninstall ? $installedModules : $availableModules
        ]);
        $model = new Module();

        if (!empty($_POST['selection'])) {
            $method = $uninstall ? 'uninstallModule' : 'installModule';
            foreach ($_POST['selection'] as $k => $module) {
                $this->{$method}($module, $installedModules, $availableModules);
            }
            return $this->message('配置保存成功', 'success');
        }

        return $this->render('index', [
            'uninstall' => $uninstall,
            'model' => $model,
            'installedModules' => $installedModules,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 安装
     * @param $module 模块标识名
     * @param $installedModules 已安装的模块
     * @param $modules 当前可用的所有模块
     * @return mixed
     */
    public function installModule($module, $installedModules, $modules)
    {
        if (!array_key_exists($module, $modules) || array_key_exists($module, $installedModules)) {
            return;
        }
        $installer = Yii::createObject([
            'class' => ModuleInstaller::className()
        ]);
        $installer->getModel()->setAttributes($modules[$module]);
        return $installer->install();
    }

    /**
     * 卸载
     * @param $module 模块标识名
     * @param $installedModules 已安装的模块
     * @param $modules 当前可用的所有模块
     * @return mixed
     */
    public function uninstallModule($module, $installedModules, $modules)
    {
        if (!array_key_exists($module, $modules) || !array_key_exists($module, $installedModules) ) {
            return ;
        }
        $installer = Yii::createObject([
            'class' => ModuleInstaller::className(),
            'model' => $installedModules[$module]
        ]);
        return $installer->uninstall();
    }
}
