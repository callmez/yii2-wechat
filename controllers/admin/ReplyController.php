<?php

namespace callmez\wechat\controllers\admin;


use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Rule;
use callmez\wechat\models\RuleSearch;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\components\AdminController;

/**
 * ReplyController implements the CRUD actions for Rule model.
 */
class ReplyController extends AdminController
{
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rule model.
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
     * Creates a new Rule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->updateModel(new Rule());
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->updateModel($this->findModel($id));
    }

    /**
     * Deletes an existing Rule model.
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
     * Finds the Rule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function updateModel(Rule $model)
    {
        $redirect = false;
        if ($model->load(Yii::$app->request->post()) && ($model->wid = $this->getMainWechat()->model->id) && $model->save()) {
            $redirect = ['update', 'id' => $model->id];
        }
        $ruleKewordModel = new RuleKeyword();
        $keywords = $this->loadKeywordModel($model, $ruleKewordModel);
        if ($redirect && empty($keywords)) {
            return $this->redirect($redirect);
        }
        return $this->render('update', [
            'model' => $model,
            'keywords' => $keywords,
            'ruleKewordModel' => $ruleKewordModel,
        ]);
    }

    protected function loadKeywordModel(Rule $rule, RuleKeyword $keyword = null)
    {
        $keywords = [];
        if (!$rule->getIsNewRecord()) {
            $post = Yii::$app->request->post($keyword->formName(), []);
            if (!empty($post['new'])) {
                $keyword === null && $keyword = new RuleKeyword();
                foreach ($post['new'] as $k => $data) {
                    $_keyword = clone $keyword;
                    $data['rid'] = $rule->id;
                    $_keyword->setAttributes($data);
                    if ($_keyword->save()) {
                        continue;
                    }
                    $keywords[] = $_keyword;
                }
            }
        }
        return $keywords;
    }
}
