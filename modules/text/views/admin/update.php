<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '修改回复规则: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '回复列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php AdminPanel::begin(['options' => ['class' => 'reply-update']]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'ruleKeyword' => $ruleKeyword,
        'ruleKeywords' => $ruleKeywords
    ]) ?>

<?php AdminPanel::end() ?>
