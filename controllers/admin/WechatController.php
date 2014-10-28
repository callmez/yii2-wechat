<?php

namespace callmez\wechat\controllers\admin;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Wechat;
use callmez\wechat\models\WechatSearch;
use callmez\wechat\models\AccountForm;

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
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Wechat model.
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
     * Creates a new Wechat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Wechat();
        $accountModel = $this->loadAccountModel($model);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'accountModel' => $accountModel
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
        $accountModel = new AccountForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'accountModel' => $accountModel
            ]);
        }
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
}
