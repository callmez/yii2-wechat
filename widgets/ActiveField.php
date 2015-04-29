<?php
namespace callmez\wechat\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use callmez\wechat\assets\FileApiAsset;

class ActiveField extends \yii\bootstrap\ActiveField
{

    /**
     * 上传文本控件模板
     * @var string
     */
    public $fileApiTemplate = "{beginLabel}\n{labelTitle}\n{endLabel}\n{beginWrapper}\n<div class=\"input-group\">\n{input}\n<div class=\"input-group-btn\">\n<button type=\"button\" class=\"btn btn-default file-input\">\n{buttonInput}\n<span>\n{buttonText}\n</span>\n</button>\n</div>\n</div>\n{hint}\n{endWrapper}";

    /**
     * 上传文本控件
     * @param array $options
     * @return static
     */
    public function fileApiInput($options = [])
    {
        if (!isset($options['template'])) {
            $this->template = $this->fileApiTemplate;
        } else {
            $this->template = $options['template'];
            unset($options['template']);
        }

        $this->parts['{buttonInput}'] = Html::activeInput('file', $this->model, $this->attribute, ArrayHelper::remove($options, 'buttonInputOptions', []));
        $this->parts['{buttonText}'] = ArrayHelper::remove($options, 'buttonText', '选择文件');

        $jsOptions = ArrayHelper::remove($options, 'jsOptions', []);
        if ($jsOptions !== false) {
            $id = $this->wrapperOptions['id'] = isset($this->wrapperOptions['id']) ? $this->wrapperOptions['id'] : 'fileApi';
            $jsOptions = Json::encode(ArrayHelper::merge([
                'url' => Yii::$app->getRequest()->getAbsoluteUrl(),
                'autoUpload' => true,
                'onUpload' => new JsExpression ("function(evt, uiEvt) {
    var fileInput = $(this).find('[type=file]');
    var text = fileInput.siblings('span').text();
    fileInput.data('text', text).siblings('span').text('上传中...');
}"),
                'onProgress' => new JsExpression ("function(evt, uiEvt) {
                var a = $(this).find('[type=file]').parent();
    $(this).find('[type=file]').siblings('span').text('上传中(' + (uiEvt.loaded / uiEvt.total * 100) + '%)...');
}"),
                'onComplete' => new JsExpression ("function(evt, uiEvt) {
    var _this = $(this);
    var fileInput = _this.find('[type=file]');
    var text = fileInput.data('text');
    if (text) {
        _this.find('[type=file]').siblings('span').text(text)
    }
    if (uiEvt.error) {
        return alert('上传错误:' + uiEvt.error);
    } else if (result.type != 'success') {
        return alert('上传失败:' + result.message);
    }
    evt.widget.\$el.find('#' + _this.getAttr('id') + '[type=text]').val(result.message);
}")
            ], $jsOptions));
            $view = $this->form->getView();
            $view->registerJs("$('#{$this->form->getId()} #{$id}').fileapi({$jsOptions});");
            FileApiAsset::register($view);
        }

        return $this->textInput($options);
    }
}