<?php

namespace callmez\wechat\controllers\admin;


use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\Rule;
use callmez\wechat\models\RuleSearch;
use callmez\wechat\models\RuleKeyword;
use callmez\wechat\models\Module;
use callmez\wechat\components\WechatAdminController;

/**
 * 消息回复管理
 */
class ReplyController extends WechatAdminController
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
     * 显示规则列表
     * @param string $module 模块规则, 为空则为自动回复规则
     * @return string
     */
    public function actionIndex($module = null)
    {
        $searchModel = new RuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->filterModule($module);

        return $this->render('index', [
            'module' => $module,
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
    public function actionCreate($module = null)
    {
        $rule = new Rule();
        $rule->type = Rule::TYPE_REPLY;
        return $this->updateModel($rule, $module);
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $module = null)
    {
        return $this->updateModel($this->findModel($id), $module);
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

    public function updateModel(Rule $model, $module = null)
    {
        $redirect = false;
        $request = Yii::$app->request;
        if ($model->load($request->post()) && ($model->wid = $this->getWechat()->model->id) && $model->save()) {
            $redirect = ['update', 'id' => $model->id];
        }
        $ruleKewordModel = new RuleKeyword();
        $keywords = $this->loadKeywordModel($model, $ruleKewordModel);
        if ($redirect && empty($keywords)) {
            return $this->redirect($redirect);
        }
        $statuses = $model::$statuses;
        unset($statuses[$model::STATUS_DELETED]);
        $modules = array_map(function($module) {
            return $module->name;
        }, Module::getServiceModules('processor'));
        return $this->render('update', [
            'model' => $model,
            'module' => $module,
            'modules' => $modules,
            'statuses' => $statuses,
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
