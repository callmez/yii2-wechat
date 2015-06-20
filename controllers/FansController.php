<?php

namespace callmez\wechat\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Fans;
use callmez\wechat\models\Message;
use callmez\wechat\models\FansSearch;
use callmez\wechat\models\MessageHistorySearch;
use callmez\wechat\components\AdminController;

/**
 * FansController implements the CRUD actions for Fans model.
 */
class FansController extends AdminController
{
    /**
     * Lists all Fans models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 用户查看
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMessage($id)
    {
        $model = $this->findModel($id);
        $message = new Message($this->getWechat());

        if ($message->load(Yii::$app->request->post())) {
            $message->toUser = $model->open_id;
            if ($message->send()) {
                $this->flash('消息发送成功!', 'success');
                $message = new Message($this->getWechat());
            }
        }
        $message->toUser = $model->open_id;

        $searchModel = new MessageHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//        $dataProvider->sort = [
//            'defaultOrder' => ['created_at' => SORT_DESC]
//        ];
        $dataProvider->query
            ->wechat($this->getWechat()->id)
            ->wechatFans($model->open_id, $this->getWechat()->original);

        return $this->render('message', [
            'model' => $model,
            'message' => $message,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 上传素材
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionUpload($id)
    {
        $model = $this->findModel($id);
        if (isset($_FILES[$model->formName()]['name'])) {
            foreach ($_POST[$model->formName()] as $attribute => $value) {
                $uploadedFile = UploadedFile::getInstance($model, $attribute);
                if ($uploadedFile === null) {
                    continue;
                } elseif ($uploadedFile->error == UPLOAD_ERR_OK) {
//                    $this->getWechat()->getSdk()->uploadMedia($this->tempName, );
                }
            }
        }
        return $this->message('上传失败', 'error');
    }

    /**
     * Updates an existing Fans model.
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
     * Finds the Fans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Fans::find()
            ->andWhere([
                'id' => $id,
                'wid' => $this->getWechat()->id
            ]);
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
