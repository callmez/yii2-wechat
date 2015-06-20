<?php
use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;
use callmez\wechat\widgets\MessageList;

$this->title = '发送客服消息';
$this->params['breadcrumbs'][] = $this->title;
$name = $model->user ? $model->user->nickname : $model->open_id;
?>

<?php PagePanel::begin([
    'title' => '和' . $name . '的聊天记录',
    'options' => [
        'class' => 'fans-message'
    ]
]) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= MessageList::widget([
                'dataProvider' => $dataProvider
            ]) ?>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-sm-12">
            <?= $this->render('_messageForm', [
                'model' => $message,
                'uploadUrl' => ['/wechat/media/upload', 'id' => $model->id]
            ]) ?>
        </div>
    </div>
<?php PagePanel::end() ?>
