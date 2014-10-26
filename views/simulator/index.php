<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use callmez\wechat\helpers\WechatHelper;

$this->title = '微信请求模拟器';
?>
<?= Html::beginForm('', 'post', [
    'id' => 'wechatForm',
    'class' => 'form-horizontal'
])
?>
    <div class="row">
        <div class="col-sm-6">
            <div class="page-header"><h4><?= Html::encode($this->title) ?></h4></div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-block btn-primary">发送</button>
                </div>
            </div>
            <div class="form-group">
                <label for="wechat" class="col-sm-2 control-label">公 众 号</label>
                <div class="col-sm-10">
                    <?php
                        $wechatApiLinks = [];
                        $wechatNames = ['请选择公众号'];
                        $timestamp = $_SERVER['REQUEST_TIME'];

                        foreach ($wechats as $k => $v) {
                            $wechatNames[$k] = $v['name'];
                            $wechatApiLinks[$k]['data']['orginal'] = $v['orginal'];
                            $wechatApiLinks[$k]['data']['api-link'] = WechatHelper::getApiLink([
                                'hash' => $v['hash'],
                                'token' => $v['token']
                            ]);
                        }
                    ?>
                    <?= Html::dropDownList('wechat', null, $wechatNames, [
                        'class' => 'form-control',
                        'options' => $wechatApiLinks
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">消息类型</label>
                <div class="col-sm-10">
                    <?= Html::radioList('type', null, array_combine(array_keys($typeArray), ArrayHelper::getColumn($typeArray, 'label')), [
                        'item' => function($index, $label, $name, $checked, $value) use ($typeArray){
                            $options = [
                                'value' => $value,
                                'label' => Html::encode($label),
                                'labelOptions' => [
                                    'class' => 'checkbox-inline'
                                ]
                            ];
                            if (isset($typeArray[$value]['target'])) {
                                $options['data-show-target'] = '[name=' . ltrim(implode('],[name=', (array)$typeArray[$value]['target']), '],') . ']';
                            }
                            return Html::radio($name, $checked, $options);
                        }
                    ])?>
                </div>
            </div>
            <div class="form-group">
                <label for="from" class="col-sm-2 control-label">发送用户</label>
                <div class="col-sm-10">
                    <?= Html::textInput('from', null, [
                        'class' => 'form-control',
                        'placeholder' => '发送用户的OpenID'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="to" class="col-sm-2 control-label">接收用户</label>
                <div class="col-sm-10">
                    <?= Html::textInput('to', null, [
                        'class' => 'form-control',
                        'placeholder' => '接受用户的OpenID(选中了公众号之后,可以不填写)'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="content" class="col-sm-2 control-label">发送内容</label>
                <div class="col-sm-10">
                    <?= Html::textarea('content', null, [
                        'class' => 'form-control',
                        'rows' => 6,
                        'placeholder' => '发送给指定用户的内容'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">图片地址</label>
                <div class="col-sm-10">
                    <?= Html::textInput('pic_url', null, [
                        'class' => 'form-control',
                        'placeholder' => '填写图片地址(暂只支持网络图片)'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">X 坐 标</label>
                <div class="col-sm-10">
                    <?= Html::textInput('location_x', null, [
                        'class' => 'form-control',
                        'placeholder' => '例如: 10.000001'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">Y 坐 标</label>
                <div class="col-sm-10">
                    <?= Html::textInput('location_y', null, [
                        'class' => 'form-control',
                        'placeholder' => '例如: 10.000001'
                    ]) ?>
                </div>
            </div>
            <div id="" class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">链接地址</label>
                <div class="col-sm-10">
                    <?= Html::textInput('url', null, [
                        'class' => 'form-control',
                        'placeholder' => '发送的链接地址'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">EventKey</label>
                <div class="col-sm-10">
                    <?= Html::textInput('event_key', null, [
                        'class' => 'form-control',
                        'placeholder' => '菜单事件名'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">发送消息</label>
                <div class="col-sm-10">
                    <?= Html::textarea('send', null, [
                        'class' => 'form-control',
                        'disabled' => true,
                        'rows' => 8
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">接收消息</label>
                <div class="col-sm-10">
                    <?= Html::textarea('receive', null, [
                        'class' => 'form-control',
                        'disabled' => true,
                        'rows' => 5
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div id="preview" style="max-width:300px">
                <div class="page-header"><h4>预览效果 <small><a id="cleanPreview" href="javascript:;">清空记录</a></small></h4></div>
            </div>
        </div>
    </div>
<?= Html::endForm(); ?>
<script id="sendTemplate" type="text/html">
<div class="send clearfix">
    <img class="avatar" class="img-rounded" src="<%= avatar %>">
    <div class="content"><%= content %></div>
</div>
</script>
<script id="receiveTemplate" type="text/html">
    <div class="receive clearfix">
    <% if (typeof content == 'object') { %>
        <ul class="news">
            <% if (content.length == 1) { %>
                <li class="clearfix">
                    <a href="<%= content[0].url || 'javascrpt:;' %>" target="_blank">
                        <div class="single">
                            <span class="alt"><%= content[0].title %></span>
                            <span class="time"><?= date('m月d日') ?></span>
                            <%= (content[0].picurl ? '<img src="' + content[0].picurl + '" />' : '') %>
                            <p class="desc"><%= content[0].description %></p>
                            <span class="link">查看全文</span>
                        </div>
                    </a>
                </li>
            <% } else { %>
                <% for (var i = 0; i < content.length; i++) { %>
                    <li class="clearfix">
                        <a href="<%= content[i].url || 'javascrpt:;' %>" target="_blank">
                            <% if(i == 0) { %>
                                <div class="top">
                                    <span class="alt"><%= content[i].title %></span>
                                    <%= (content[i].picurl ? '<img src="' + content[i].picurl + '" />' : '') %>
                                </div>
                            <% } else { %>
                                <div class="list">
                                    <%= (content[i].picurl ? '<img src="' + content[i].picurl + '" />' : '') %>
                                    <span class="alt"><%= content[i].title %></span>
                                </div>
                            <% } %>
                        </a>
                    </li>
                <% } %>
            <% } %>
        </ul>
    <% } else { %>
        <img class="avatar" src="<%= avatar %>">
        <div class="content"><%= content %></div>
    <% } %>
    </div>
</script>
<script id="errorTemplate" type="text/html">
    <p class="text-danger"> <small><%= content %></small> </p>
</script>
<?php
$css = <<<EOF
.checkbox-inline { padding-left: 1px; margin-left: 5px !important; }
.avatar { width: 34px; height: 34px; border-radius: 3px; }
.content { max-width: 218px; font-size: 14px; line-height: 1.42857143; border: 1px solid transparent; border-radius: 3px; padding: 6px 12px; }
.send, .receive { margin-bottom: 15px; }
.send { text-align: right; }
.send .avatar, .send .content { float: right; color: #fff; background-color: #5cb85c; border-color: #4cae4c; }
.send .content { margin-right: 7px; }
.receive { text-align: left; }
.receive .avatar, .receive .content { float: left; }
.receive .content { margin-left: 7px; color: #000; background-color: #fff; border-color: #dcdcdc; }
.news { list-style: none; padding: 0; border: 1px solid #cdcdcd; box-shadow: 0 3px 6px #999; -webkit-border-radius: 12px; -moz-border-radius: 12px; border-radius: 2px; background-color: #FFF; background: -webkit-gradient(linear,left top,left bottom,from(#FFFFFF),to(#FFFFFF)); background-image: -moz-linear-gradient(top,#FFFFFF 0%,#FFFFFF 100%); margin: 0px auto;}
.news a { color: #000; }
.news a:hover { text-decoration: none; }
.news img { width: 100%; }
.news li { padding: 9px; border-bottom: 1px solid #ccc; color: #000; position: relative;}
.news li .top { position: relative; }
.news li .top .alt { color: #fff; display: block; position: absolute; bottom: 0; left: 0; width: 100%; padding: 4px; background: rgba(0, 0, 0, 0.5); }
.news li .list { margin-bottom: 15px; }
.news li .list img { width: 45px; height: 45px; float: right; }
.news li .list .alt { margin-right: 100px; display: block; }
.news li .single { padding: 6px; }
.news li .single .alt { display: block; line-height: 1.2em; text-align: left; font-size: 20px; }
.news li .single .time { display: block; color: #8c8c8c; font-size: 12px; margin: 10px 0; }
.news li .single .desc { color: #8c8c8c; margin: 10px 0 20px }
EOF;
$js = <<<EOF
// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
(function(){
  var cache = {};

  this.tmpl = function tmpl(str, data){
    // Figure out if we're getting a template, or if we need to
    // load the template - and be sure to cache the result.
    var fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :

      // Generate a reusable function that will serve as a template
      // generator (and which will be cached).
      new Function("obj",
        "var p=[],print=function(){p.push.apply(p,arguments);};" +

        // Introduce the data as local variables using with(){}
        "with(obj){p.push('" +

        // Convert the template into pure JavaScript
        str
          .replace(/[\\r\\t\\n]/g, " ")
          .split("<%").join("\\t")
          .replace(/((^|%>)[^\\t]*)'/g, "$1\\r")
          .replace(/\\t=(.*?)%>/g, "',$1,'")
          .split("\\t").join("');")
          .split("%>").join("p.push('")
          .split("\\r").join("\\\\'")
      + "');}return p.join('');");

    // Provide some basic currying to the user
    return data ? fn( data ) : fn;
  };
})();
/**
 *   example 1: nl2br('Kevin\\nvan\\nZonneveld');
 *	 returns 1: 'Kevin<br />\\nvan<br />\\nZonneveld'
 *	 example 2: nl2br("\\nOne\\nTwo\\n\\nThree\\n", false);
 *	 returns 2: '<br>\\nOne<br>\\nTwo<br>\\n<br>\\nThree<br>\\n'
 *	 example 3: nl2br("\\nOne\\nTwo\\n\\nThree\\n", true);
 *	 returns 3: '<br />\\nOne<br />\\nTwo<br />\\n<br />\\nThree<br />\\n'
 */
function nl2br(str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display
    return (str + '').replace(/([^>\\r\\n]?)(\\r\\n|\\n\\r|\\r|\\n)/g, '$1' + breakTag + '$2');
}
function json2xml(data, root) {
    var xml = '';
    if (root == undefined) root = true;
    if (typeof data == 'object') {
        $.each(data, function(key, data) {
            data = typeof data == 'object' ? json2xml(data, false) : '<![CDATA[' + data + ']]>';
            xml += '<' + key + '>' + data + '</' + key + ">\\n";
        });
    } else {
        xml = data;
    }

    return root == true ? "<xml>\\n" + xml + "</xml>" : xml;
}
function xml2dom(xml){
    var data = null;
    try{
        if(window.ActiveXObject){
            data = new ActiveXObject("Microsoft.XMLDOM");
            data.async = "false";
            data.loadXML(xml);
        } else {// 用于 Mozilla, Firefox, Opera, 等浏览器的代码：
           var parser = new DOMParser();
           data = parser.parseFromString(xml, "text/xml");
        }
    } catch(e) {
        alert("您的浏览器不支持模拟测试");
    }
    return data;
}
function renderMessage(data, type)
{
    var template = '',
        params = {};

    if (typeof data === 'string') {
        params.content = data;
    } else {
        params.avatar = avatar;
        if (type == 'send') {
            switch(data.MsgType) {
                case 'text':
                    params.content = data.Content;
                    break;
                case 'image':
                    params.content = '<img src="' + data.PicUrl + '" />';
                    break;
                case 'location':
                    params.content = '<img src="' + locationImg + '" />';
                    break;
                case 'link':
                    params.content = data.Url;
                    break;
                case 'subscribe':
                    params.content = '关注事件';
                    break;
                case 'unsubscribe':
                    params.content = '取消关注事件';
                    break;
                case 'event':
                    var eventKey = data.EventKey;
                    eventKey = data.Event == 'subscribe' ? '关注' : (data.Event == 'unsubscribe' ? '取消关注' : eventKey);
                    params.content = '事件: '+ eventKey;
                    break;
            }
        } else if (type == 'receive') {
            var msgType = $('MsgType', data).html();
            switch(msgType) {
                case 'text':
                    params.content = nl2br($('Content', data).html());
                    break;
                case 'news':
                    var title = $('Title', data);
                    if (title.length) {
                        params.content = [];
                        title.each(function(i) {
                            params.content.push({
                                title: $('Title:eq(' + i + ')', data).html(),
                                description: $('Description:eq(' + i + ')', data).html(),
                                picurl: $('PicUrl:eq(' + i + ')', data).html(),
                                url: $('Url:eq(' + i + ')', data).html()
                            });
                        });
                    }
                    break;
                default:
                    type = 'error';
                    params = {content: '暂不支持' + data.MsgType + '格式显示'}
                    return;
            }
        }
    }
    $('#preview').append(tmpl(type + 'Template', params));
}

var avatar = 'data:image/jpg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMsaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjUtYzAxNCA3OS4xNTE0ODEsIDIwMTMvMDMvMTMtMTI6MDk6MTUgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MTIxQjlGOTUwNTUxMUU0QjZFNkY3OUQyNzkxMjJCNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MTIxQjlGQTUwNTUxMUU0QjZFNkY3OUQyNzkxMjJCNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYxMjFCOUY3NTA1NTExRTRCNkU2Rjc5RDI3OTEyMkI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjYxMjFCOUY4NTA1NTExRTRCNkU2Rjc5RDI3OTEyMkI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgG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',
    locationImg = 'data:image/jpg;base64,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';

// 消息类型切换
var showTarget = $([]);
$('[name=type]').each(function(){
    var target = $(this).data('show-target');
    if (target) showTarget = showTarget.add(target);
}).click(function() {
    showTarget.closest('.form-group').hide();
    var target = $(this).data('show-target');
    if (target) {
        showTarget.filter(target)
            .closest('.form-group')
            .show();
    }
}).eq(0).click();

//插入选择的公众号值
$('[name=wechat]').change(function(){
    $('[name=to]').val($('option:selected', this).data('orginal'));
});

$('#previewClean').click(function(){
    $('#prview').empty();
})
//提交表单
$('#wechatForm').submit(function(e){
    e.preventDefault();

    var _this = $(this),
        type = $('[name=type]:checked', _this).val(),
        data, sendXml, receiveXml;

    //创建发送的数据
    switch(type) {
        case 'text':
            data = {
                MsgType: type,
                Content: $('[name=content]', _this).val()
            };
            break;
        case 'image':
            data = {
                MsgType: type,
                PicUrl: $('[name=pic_url]', _this).val()
            };
            break;
        case 'location':
            data = {
                MsgType: type,
                Location_X: $('[name=location_x]', _this).val(),
                Location_Y: $('[name=location_y]', _this).val(),
                Scale: 20,
                Label: '位置信息'
            };
            break;
        case 'link':
            data = {
                MsgType: type,
                Title: '测试链接',
                Description: '测试链接描述',
                Url: $('[name=url]', _this).val()
            };
            break;
        case 'subscribe':
        case 'unsubscribe':
            data = {
                MsgType: 'event',
                Event: type,
                EventKey: ''
            };
            break;
        case 'event':
            data = {
                MsgType: type,
                Event: 'CLICK',
                EventKey: $('[name=url]', _this).val()
            };
            break;
    }
    data = $.extend({
        ToUserName: $('[name=to]', _this).val(),
        FromUserName: $('[name=from]', _this).val(),
        CreateTime: Math.round(new Date().getTime()/1000)
    }, data, {
        MsgId: 1234567890123456
    });
    renderMessage(data, 'send');
    sendXml = json2xml(data);
    $('[name=send]', _this).val(sendXml);

    var receive = $('[name=receive]'),
        apiLink = $('[name=wechat] option:selected', _this).data('api-link');
    $.ajax(apiLink, {
        type : 'POST',
        headers : {
            "Content-type": "text/xml"
        },
        data: sendXml.replace(/[\\r\\n]/g, ''),
        beforeSend : function(){
            receive.text('加载中...');
        }
    }).done(function(response) {
        if (!response) return ;
        var data = $(response).filter('xml');
        if(data.length){
            renderMessage(data, 'receive');
        } else {
            renderMessage('Parse error!', 'error');
        }
        receive.val(response);
    }).fail(function() {
        renderMessage('Post fail!', 'error');
    });
});
EOF;
$this->registerCss($css);
$this->registerJs($js);
?>