<?php
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
            <p class="alert <?= $statusSettings[$status]['alertClass'] ?>">
                <?= $message ?>
            </p>
            <?php if ($redirect): ?>
            <p>
                <a href="<?= $redirect ?>"><span id="time">3</span>秒后即将跳转页面</a>
            </p>
            <?php $this->registerJs('
            var time = 3,
                $time = $("#time");
            setInterval(function(){
                $time.html(time--);
                if (time < 0) {
                    window.location.href="' . $redirect . '"
                }
            }, 1000)
            ') ?>
            <?php endif ?>
            <p>
                <a href="javascript:history.go(-1);">返回上一页</a>
            </p>
        </div>
    </div>
</div>