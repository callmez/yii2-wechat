<?php
namespace callmez\wechat\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Media;
use callmez\wechat\models\MediaForm;
use callmez\wechat\models\MediaSearch;
use callmez\wechat\models\MediaNewsForm;
use callmez\wechat\components\AdminController;

class MediaController extends AdminController
{

    /**
     * 选择文件(ajax触发)
     * @return string
     */
    public function actionPick()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pick', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 上传图片接口
     * @throws NotFoundHttpException
     */
    public function actionUpload()
    {
        $model = new MediaForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->upload()) {
                return $this->message('上传成功', 'success');
            }
        }
//        $files = static::getFiles();
//        if (!empty($files)) {
//            if (($mediaType = Yii::$app->request->post('mediaType')) === null) {
//                return $this->message('错误的媒体素材类型!', 'error');
//            }
//            foreach ($files as $name) {
//                $_model = clone $model;
//                $_model->setAttributes([
//                    'type' => $mediaType,
//                    'material' => ''
//                ]);
//                $uploadedFile = UploadedFile::getInstanceByName($name);
//                $result = $this->getWechat()->getSdk()->uploadMedia($uploadedFile->tempName, $mediaType);
//            }
//        }
        return $this->render('upload', [
            'model' => $model
        ]);
    }

    /**
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Media model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Media model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $media = new MediaForm($this->getWechat());
        $news = new MediaNewsForm();

        $request = Yii::$app->request;
        if ($request->getIsPost()) {
            $post = $request->post();
            switch ($request->post('mediaType')) {
                case Media::TYPE_MEDIA:
                    $media->load($post);
                    $media->file = UploadedFile::getInstance($media, 'file');
                    if ($media->save()) {
                        return $this->message('操作成功!', 'success');
                    }
                    break;
                case Media::TYPE_NEWS:
                    $news->load($post);
                    if ($news->save()) {

                    }
                    break;
                default:
                    throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->render('create', [
            'media' => $media,
            'news' => $news
        ]);
    }

    /**
     * Updates an existing Media model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Media model.
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
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @var array
     */
    private static $_files;

    /**
     * 获取上传文件
     * @return array
     */
    public static function getFiles()
    {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $class => $info) {
                    self::getUploadFilesRecursive($class, $info['name'], $info['error']);
                }
            }
        }
        return self::$_files;
    }

    /**
     * 递归查询上传文件
     * @param $key
     * @param $names
     * @param $errors
     */
    protected static function getUploadFilesRecursive($key, $names, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                static::getUploadFilesRecursive($key . '[' . $i . ']', $name, $errors);
            }
        } elseif ($errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[] = $key;
        }
    }

}