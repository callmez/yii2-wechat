<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

$this->title = '添加回复规则';
$this->params['breadcrumbs'][] = ['label' => '回复列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php AdminPanel::begin(['options' => ['class' => 'reply-create']]) ?>

    <?= $this->render('_form', [
        'model' => $model,
        'ruleKeyword' => $ruleKeyword,
        'ruleKeywords' => $ruleKeywords
    ]) ?>

<?php AdminPanel::end() ?>
