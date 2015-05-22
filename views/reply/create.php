<?php

use yii\helpers\Html;
use callmez\wechat\widgets\PagePanel;

$this->title = '添加回复规则';
$this->params['breadcrumbs'][] = ['label' => '回复规则', 'url' => ['index', 'mid' => $mid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php PagePanel::begin(['options' => ['class' => 'reply-create']]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'ruleKeyword' => $ruleKeyword,
        'ruleKeywords' => $ruleKeywords
    ]) ?>

<?php PagePanel::end() ?>
