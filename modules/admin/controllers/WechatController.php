<?php

namespace callmez\wechat\modules\admin\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use callmez\wechat\modules\admin\models\WechatForm;
use callmez\wechat\modules\admin\models\WechatSearch;
use callmez\wechat\modules\admin\components\Controller;

/**
 * 微信公众号管理
 * @package callmez\wechat\modules\admin\controllers
 */
class WechatController extends Controller
{
    /**
     * Lists all Wechat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WechatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 设置当前管理的公众好
     * @param $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionManage($id)
    {
        $model = $this->findModel($id);
        $this->setWechat($model);
        $this->flash('当前管理公众号设置为"' . $model->name . '", 您现在可以管理该公众号了', 'success');
        return $this->redirect(['/wechat/admin/default']);
    }

    /**
     * Creates a new Wechat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WechatForm();

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST['ajax'])) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } elseif ($model->save()) {
                return $this->flash('添加成功!', 'success', ['update', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Wechat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (isset($_POST['ajax'])) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } elseif ($model->save()) {
                $this->flash('更新成功', 'success');
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Wechat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Wechat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wechat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WechatForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
