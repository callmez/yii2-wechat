<?php
use \Yii;
use yii\helpers\ArrayHelper;
$statusSettings = [
    'success' => [
        'alertClass' =>  'alert-success'
    ],
    'info' => [
        'alertClass' =>  'alert-info'
    ],
    'error' => [
        'alertClass' =>  'alert-danger'
    ],
    'warning' => [
        'alertClass' =>  'alert-warning'
    ],
]
?>
<div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <div class="page-header">
            <h4>信息提示</h4>
        </div>
        <div class="text-center">
            <p class="alert <?= $statusSettings[$type]['alertClass'] ?>">
                <?= is_array($message) ? json_encode($message) : $message?>
            </p>
            <?php if ($redirect): ?>
                <p>
                    <a href="<?= $redirect ?>"><span id="time">3</span>秒后即将跳转页面</a>
                </p>
            <?php $this->registerJs(<<<EOF
var time = 3,
    _time = $("#time");
setInterval(function(){
    _time.html(time--);
    if (time < 0) {
        window.location.href="{$redirect}"
    }
}, 1000)
EOF
            ) ?>
            <?php endif ?>
            <p>
                <?php if ($referrer = Yii::$app->request->getReferrer()): ?>
                    <a href="javascript:window.location.href = '<?= $referrer ?>';">返回上一页</a>
                <?php else: ?>
                    <a href="javascript:window.location.href = '<?= Yii::$app->request->getBaseUrl() ?>';">跳转首页</a>
                <?php endif ?>
            </p>
        </div>
    </div>
</div>