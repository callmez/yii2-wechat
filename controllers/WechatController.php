<?php

namespace callmez\wechat\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\helpers\Request;
use callmez\wechat\models\WechatForm;
use callmez\wechat\models\WechatSearch;
use callmez\wechat\components\AdminController;

/**
 * 微信公众号管理
 * @package callmez\wechat\controllers
 */
class WechatController extends AdminController
{
    /**
     * 关闭公众号设置检查
     * @var bool
     */
    public $enableCheckWechat = false;
    /**
     * 显示所有公众号
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
     * 设置当前管理的公众号
     * @param $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionManage($id)
    {
        $model = $this->findModel($id);
        $this->setWechat($model);
        $this->flash('当前管理公众号设置为"' . $model->name . '", 您现在可以管理该公众号了', 'success');
        return $this->redirect(['/wechat']);
    }

    /**
     * 创建公众号
     * @return string
     */
    public function actionCreate()
    {
        $model = new WechatForm();
        if ($model->load(Yii::$app->request->post())) {
            if (Request::isAjax()) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } elseif ($model->save()) {
                return $this->flash('公众号创建成功! 请补充余下公众号设置以激活公众号', 'info', ['update', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }
    
    /**
     * 头像,二维码上传
     * @param $id
     * @return array|bool|string
     * @throws NotFoundHttpException
     */
    public function actionUpload($id)
    {
        $model = $this->findModel($id);
        $formName = $model->formName();
        $attribute = isset($_POST[$formName]['avatar']) ? 'avatar' : 'qrcode';
        $model->$attribute = UploadedFile::getInstance($model, $attribute);
        $model->setScenario($attribute . 'Upload');
        if ($model->$attribute && $model->validate()) {
            $path = '/wechat/' . $attribute . '_' . $model->id . '.' . $model->$attribute->getExtension();
            $realPath = Yii::getAlias('@storageRoot' . $path);
            FileHelper::createDirectory(dirname($realPath));
            if ($model->$attribute->saveAs($realPath)) {
                return $this->message(['path' => $path], 'success');
            } else {
                return $this->message('上传失败, 无法保存上传文件!');
            }
        }
        return $this->message('图片上传失败' . ($model->hasErrors() ? ':' . array_values($model->getFirstErrors())[0] : ''));
    }

    /**
     * 修改公众号信息
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if (Request::isAjax()) {
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } elseif ($model->save()) {
                return $this->flash('更新成功', 'success');
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除公众号
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
