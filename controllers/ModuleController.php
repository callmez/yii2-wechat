<?php

namespace callmez\wechat\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Module;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\components\AdminController;

/**
 * 扩展模块管理
 */
class ModuleController extends AdminController
{
    /**
     * 显示所有可用的扩展模块
     * @return mixed
     */
    public function actionIndex()
    {
        $models = $this->findInstalledModules();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_merge(ModuleHelper::findAvailableModules(), $models),
            // TODO 多model排序字段相同的话,会报错 @see https://github.com/yiisoft/yii2/issues/8348
//            'sort' => [
//                'attributes' => ['type'],
//                'defaultOrder' => [
//                    'type' => SORT_DESC
//                ]
//            ]
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
        if (!empty($_POST)) {
            if ($model->install(false)) {
                return $this->flash('模块安装成功!', 'success', ['index']);
            } elseif (!$model->hasErrors()) {
                return $this->flash('模块安装失败!', 'error');
            }
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
        if (!empty($_POST)) {
            if ($model->uninstall()) {
                return $this->flash('模块卸载成功!', 'success', ['index']);
            } else {
                return $this->flash('模块卸载失败!', 'error');
            }
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
        if (($model = ModuleHelper::findAvailableModuleById($id)) !== null) {
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
        if (($model = Module::findOne($id)) !== null) {
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
        return Module::find()->indexBy('id')->all();
    }
}
