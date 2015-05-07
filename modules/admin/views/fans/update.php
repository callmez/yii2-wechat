<?php

use yii\helpers\Html;
use callmez\wechat\modules\admin\widgets\AdminPanel;

/* @var $this yii\web\View */
/* @var $model callmez\wechat\models\Fans */

$this->title = '修改粉丝';
$this->params['breadcrumbs'][] = ['label' => '粉丝管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>
<?php AdminPanel::begin(['options' => ['class' => 'fans-update']]) ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
