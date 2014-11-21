<?php

namespace callmez\wechat\controllers\admin;

use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\base\InvalidCallException;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\WechatSearch;
use callmez\wechat\models\AccountForm;
use callmez\wechat\components\Wechat as WechatSdk;
use callmez\wechat\components\AdminController;
use callmez\storage\helpers\UploadHelper;
use callmez\storage\uploaders\AbstractUploader;

/**
 * AccountController implements the CRUD actions for Wechat model.
 */
class AccountController extends AdminController
{
    public function behaviors()
    {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

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
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Creates a new Wechat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->update(new Wechat());
    }

    /**
     * Updates an existing Wechat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->update($this->findModel($id));
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
     * 显示公众号详情
     * @param int $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id = null)
    {
        if (!($id === null && $wechat = $this->getMainWechat()) && !($wechat = WechatSdk::instanceByCondition($id))) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->render('view', [
            'wechat' => $wechat
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
        if (!($wechat = WechatSdk::instanceByCondition($id))) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $this->setMainWechat($wechat);
        Yii::$app->set('success', '设置管理公众号, 您现在可以管理该公众号了');
        return $this->redirect(['view']);
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
        if (($model = Wechat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function update(Wechat $model)
    {
        $accountModel = $this->loadAccountModel($model);

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImage($model);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '公众号信息修改成功!');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'accountModel' => $accountModel,
            'uploader' => AbstractUploader::getInstance(Yii::$app->storage->get())
        ]);
    }

    /**
     * 通过微信管理平台账户获取微信信息
     * @param Wechat $model
     * @return AccountForm
     */
    protected function loadAccountModel(Wechat $model)
    {
        $accountModel = new AccountForm();
        $post = Yii::$app->request->post();
        if ($accountModel->load($post) && $accountModel->login()) {
            $modelName = $model->formName();
            foreach ($accountModel->parse() as $k => $v) {
                if ($model->hasAttribute($k)) {
                    $post[$modelName][$k] = $v;
                }
            }
            $accountModel = new AccountForm();
            Yii::$app->request->setBodyParams($post);
        }
        return $accountModel;
    }

    /**
     * 上传二维码和图片
     * @return string
     * @throws \yii\base\InvalidCallException
     * @throws \yii\web\NotFoundHttpException
     */
    public function uploadImage(Wechat $model)
    {
        if ($storageName = Yii::$app->request->post('storage')) { // 上传图片
            if (!Yii::$app->storage->has($storageName)) {
                throw new InvalidCallException("The {$storageName} storage is not exists.");
            }
            $storage = Yii::$app->storage->get($storageName);
            $uploader = AbstractUploader::getInstance($storage);
            if (!$uploader->isUploaded()) {
                throw new NotFoundHttpException("Upload error.");
            }
            if ($uploader->validate() && $uploader->save($path = $this->getUploadPath($model, $uploader))) {
                return $this->message([
                    'value' => Yii::$app->storage->getWrapperPath($path, $storageName),
                    'thumbnail' => $storage->thumbnail($path, ['width' => 100])
                ], 'success');
            } else {
                return $this->message($uploader->getError());
            }
        }
    }

    /**
     * 生成上传路径
     * @param Wechat $model
     * @param AbstractUploader $uploader
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function getUploadPath(Wechat $model, AbstractUploader $uploader)
    {
        $attribute = isset(Yii::$app->request->post($model->formName())['avatar']) ? 'avatar' : 'qr_code';
        return UploadHelper::generateUniquePath(
            $uploader->getName(),
            "/wechat/account/{$attribute}_{key}.jpg",
            $model->original ? md5($model->original) : null
        );
    }
}
