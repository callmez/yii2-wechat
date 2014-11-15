<?php

namespace callmez\wechat\controllers\admin;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\base\InvalidCallException;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\WechatSearch;
use callmez\wechat\models\AccountForm;
use callmez\storage\helpers\UploadHelper;
use callmez\storage\uploaders\AbstractUploader;

/**
 * WechatController implements the CRUD actions for Wechat model.
 */
class WechatController extends Controller
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
        $model = new Wechat();
        $accountModel = $this->loadAccountModel($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'accountModel' => $accountModel,
                'uploader' => AbstractUploader::getInstance(Yii::$app->storage->get())
            ]);
        }
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
        $accountModel = $this->loadAccountModel($model);

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadImage($model);
            if ($model->save()) {
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
        if (($model = Wechat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
            foreach($accountModel->parse() as $k => $v) {
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
                $message = [
                    'status' => 'success',
                    'message' => [
                        'value' => "{$storageName}://" . ltrim($path, '/'),
                        'thumbnail' => $storage->thumbnail($path, [
                            'width' => 100
                        ])
                    ]
                ];
            } else {
                $message = [
                    'status' => 'error',
                    'message' => $uploader->getError()
                ];
            }

            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = $message;
            Yii::$app->end(0, $response);
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
