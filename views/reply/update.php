<?php

use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;

$this->title = '修改回复规则: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '回复规则', 'url' => ['index', 'mid' => $model->mid]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php PagePanel::begin(['options' => ['class' => 'reply-update']]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'ruleKeyword' => $ruleKeyword,
        'ruleKeywords' => $ruleKeywords
    ]) ?>

<?php PagePanel::end() ?>
