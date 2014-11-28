<?php
use yii\helpers\Url;
use yii\helpers\Html;
use callmez\wechat\models\Wechat;

$this->title = $wechat->model->name . ' 概况';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h4><span class="glyphicon glyphicon-user"></span> 公众号信息</h4>
</div>
<div class="clearfix">
    <?= Html::img(Yii::$app->storage->thumbnail($wechat->model->avatar, ['width' => 100]), [
        'class' => 'img-thumbnail pull-left'
    ]) ?>
    <dl class="pull-left dl-horizontal dl-horizontal-xs">
        <dt>公众号名称</dt><dd><?= Html::encode($wechat->model->name) ?></dd>
        <dt>公众号类型</dt><dd><?= Wechat::$types[$wechat->model->type] ?></dd>
        <dt>Token</dt><dd><?= Html::encode($wechat->model->token) ?></dd>
        <dt>接口地址</dt><dd><?= Url::to(['/wechat/api', 'hash' => $wechat->model->hash], true) ?></dd>
    </dl>
</div>