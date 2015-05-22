<?php
namespace callmez\wechat\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use callmez\wechat\assets\FileApiAsset;

/**
 * 基于FileAPI JS 上传组件的Input Widget
 *
 * @package callmez\wechat\widgets
 */
class FileApi extends InputWidget
{
    /**
     * 模板
     * @var string
     */
    public $template = "\n<div id=\"{id}\" class=\"input-group\">\n<div class=\"input-group-btn\">\n{button}\n</div>\n{input}\n</div>\n";
    /**
     * @inheritdoc
     */
    public $options = ['class' => 'form-control'];
    /**
     * 上传按钮文本
     * @var string
     */
    public $buttonText = '选择文件';
    /**
     * 上传按钮选项
     * @var array
     */
    public $buttonOptions = ['class' => 'btn btn-default file-input'];
    /**
     * FileAPI上传组件的js选项
     * @var array
     */
    public $jsOptions = [];

    /**
     * 输出模板内容
     * @return string
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
            $fileInput = Html::fileInput(Html::getInputName($this->model, $this->attribute));
        } else {
            $input = Html::textInput($this->name, $this->value, $this->options);
            $fileInput = Html::fileInput($this->name, $this->value);
        }

        $button = Html::tag('div', $fileInput . '<span>' . $this->buttonText . '</span>', $this->buttonOptions);
        return strtr($this->template, [
            '{id}' => $this->getId(),
            '{input}' => $input,
            '{button}' => $button
        ]);
    }

    /**
     * 注册FileAPI上传控制JS
     */
    public function registerClientScript()
    {
        $options = Json::htmlEncode($this->getClientOptions());
        $view = $this->getView();
        FileApiAsset::register($view);
        $view->registerJs("$('#{$this->getId()}').fileapi({$options});");
    }

    /**
     * 返回FileApi所需要的JS设置
     * 该设置默认会自动上传图片, 并会根据服务器端返回的JSON内容判断成功失败
     * 成功则会写入返回的数据到input中
     * 成功返回:
     * ```
     * {
     *     'type': 'success',
     *     'message' => {
     *         'path' => 'path'
     *     }
     * }
     * ```
     * 失败或其他返回:
     * ```
     * {
     *     'type': 'error|info|warning',
     *     'message' => 'message'
     * }
     * ```
     *
     * @return array
     */
    protected function getClientOptions()
    {
        $request = Yii::$app->getRequest();
        $options = array_merge([
            'autoUpload' => true,
            'data' => [
                $request->csrfParam => $request->getCsrfToken() // 带上csr参数
            ]
        ], $this->jsOptions);
        $options['url'] = isset($options['url']) ? Url::to($options['url'], true) : $request->getAbsoluteUrl();
        if (!isset($options['onUpload'])) {
            $options['onUpload'] = new JsExpression (<<<EOF
function(evt, uiEvt) {
    var fileInput = $(this).find('[type=file]');
    var text = fileInput.siblings('span').text();
    fileInput.data('text', text).siblings('span').text('上传中...');
}
EOF
);
        }
        if (!isset($options['onProgress'])) {
            $options['onProgress'] = new JsExpression (<<<EOF
function(evt, uiEvt) {
    $(this).find('[type=file]').siblings('span').text('上传中(' + parseInt(uiEvt.loaded / uiEvt.total * 100) + '%)...');
}
EOF
            );
        }
        if (!isset($options['onComplete'])) {
            $options['onComplete'] = new JsExpression (<<<EOF
function(evt, uiEvt) {
    var _this = $(this);
    var fileInput = _this.find('[type=file]');
    var text = fileInput.data('text');
    if (text) {
        _this.find('[type=file]').siblings('span').text(text)
    }
    if (uiEvt.error) {
        return alert('上传错误:' + uiEvt.error);
    } else if (uiEvt.result.type != 'success') {
        return alert(uiEvt.result.message);
    }
    evt.widget.\$el.find('[type=text]').val(uiEvt.result.message.path);
}
EOF
            );
        }
        return $options;
    }
}