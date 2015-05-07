<?php

namespace callmez\wechat\modules\admin\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\AddonModule;
use callmez\wechat\modules\admin\components\Controller;

/**
 * 扩展模块管理
 */
class ModuleController extends Controller
{
    /**
     * 显示所有可用的扩展模块
     * @return mixed
     */
    public function actionIndex()
    {
        $models = $this->findInstalledModules();
        $dataProvider = new ArrayDataProvider([
            'models' => array_merge(AddonModule::findAvailableModules(), $models)
        ]);

        return $this->render('index', [
            'models' => $models,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 安装模块
     * @param $id
     * @return array|bool|string
     * @throws NotFoundHttpException
     */
    public function actionInstall($id)
    {
        $model = $this->findAvailableModule($id);
        if (!$model->validate()) {
            return $this->message('模块的设置错误: ' . array_values($model->firstErrors)[0]);
        }
        if (!empty($_POST) && $model->install(false)) {
            return $this->flash('模块安装成功!', 'success', ['index']);
        }
        return $this->render('install', [
            'model' => $model
        ]);
    }

    /**
     * 卸载模块
     * @param $id
     * @return array|bool|string
     * @throws NotFoundHttpException
     */
    public function actionUninstall($id)
    {
        $model = $this->findInstalledModule($id);
        if (!$model->getCanUninstall()) {
            return $this->message(array_values($model->firstErrors)[0]);
        }
        if (!empty($_POST) && $model->uninstall()) {
            return $this->flash('模块卸载成功!', 'success', ['index']);
        }
        return $this->render('uninstall', [
            'model' => $model
        ]);
    }

    /**
     * 查找可用的扩展模块
     * @param $id
     * @return null
     * @throws NotFoundHttpException
     */
    public function findAvailableModule($id)
    {
        if (($model = AddonModule::findAvailableModuleById($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 查找已安装的模块
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findInstalledModule($id)
    {
        if (($model = AddonModule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 查找已安装的模块
     * @return array|\yii\db\ActiveRecord[]
     */
    public function findInstalledModules()
    {
        return AddonModule::find()->indexBy('id')->all();
    }
}
