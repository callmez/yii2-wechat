<?php

namespace callmez\wechat\modules\admin\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\MessageHistory;
use callmez\wechat\modules\admin\components\Controller;
use callmez\wechat\modules\admin\models\MessageHistorySearch;

/**
 * MessageHistoryController implements the CRUD actions for MessageHistory model.
 */
class MessageController extends Controller
{
    /**
     * Lists all MessageHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MessageHistory model.
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
     * Deletes an existing MessageHistory model.
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
     * 粉丝发送消息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSend($id)
    {
        $fans = $this->findModel($id);
        $searchModel = new MessageHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = [
            'defaultOrder' => ['created_at' => SORT_DESC]
        ];
        $dataProvider->query->andWhere([
            'open_id' => $fans->open_id,
            'wid' => $this->getWechat()->id,
        ]);
        $model = new CustomMessage();
        return $this->render('message', [
            'fans' => $fans,
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the MessageHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MessageHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = MessageHistory::find()
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
