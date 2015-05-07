<?php
namespace callmez\wechat\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use callmez\wechat\models\ReplyRule;
use callmez\wechat\models\ReplyRuleKeyword;
use callmez\wechat\modules\admin\models\RuleSearch;
use callmez\wechat\modules\admin\components\Controller;

/**
 * 消息回复
 * @package callmez\wechat\modules\admin\controllers
 */
class ReplyController extends Controller
{
    /**
     * Lists all Rule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([
            'wid' => $this->getWechat()->id,
        ]);
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
        $model = new ReplyRule();
        $keyword = new ReplyRuleKeyword();
        $keywords = [];
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;
            if ($model->save() && $this->saveReplyRuleKeyword($model, $keyword, $keywords)) {
                return $this->flash('添加成功!', 'success', ['update', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'keyword' => $keyword,
            'keywords' => $keywords
        ]);
    }

    /**
     * Updates an existing Rule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $keyword = new ReplyRuleKeyword();
        $keywords = $model->replyKeywords;
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;
            if ($this->saveReplyRuleKeyword($model, $keyword, $keywords) && $model->save()) {
                return $this->flash('修改成功!', 'success', ['update', 'id' => $model->id]);
            }
        }
        return $this->render('update', [
            'model' => $model,
            'keyword' => $keyword,
            'keywords' => $keywords
        ]);
    }

    protected function saveReplyRuleKeyword($rule, $keyword, $keywords = [])
    {
        $_keywords = ArrayHelper::index($keywords, 'id');
        $keywords = [];
        $valid = true;
        foreach (Yii::$app->request->post($keyword->formName(), []) as $k => $data) {
            if (!empty($data['id']) && $_keywords[$data['id']]) {
                $_keyword = $_keywords[$data['id']];
                unset($_keywords[$data['id']]);
            } else {
                $_keyword = clone $keyword;
            }
            unset($data['id']);
            $keywords[] = $_keyword;
            $_keyword->setAttributes(array_merge($data, [
                'rid' => $rule->id
            ]));
            $valid = $valid && $_keyword->save();
        }
        !empty($_keywords) && ReplyRuleKeyword::deleteAll(['id' => array_keys($_keywords)]); // 无更新的则删除
        return $valid;
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
        $query = ReplyRule::find()
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