<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = '微信请求模拟器';

$form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal'
    ]
]);
?>
    <h4><?= Html::encode($this->title) ?></h4>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-block btn-primary">发送</button>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">公 众 号</label>
                <div class="col-sm-10">
                    <?= Html::dropDownList('wechat', null, ['请选择公众号'], [
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">消息类型</label>
                <div class="col-sm-10">
                    <?= Html::radioList('type', null, [
                        'text' => '文本',
                        'image' => '图片',
                        'location' => '位置',
                        'link' => '链接',
                        'event' => '菜单',
                        'subscribe' => '关注',
                        'unsubscribe' => '取消关注',
                        'other' => '其他',
                    ], [
                        'itemOptions' => [
                            'labelOptions' => [
                                'class' => 'checkbox-inline'
                            ]
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">发送用户</label>
                <div class="col-sm-10">
                    <?= Html::textInput('from', null, [
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">接收用户</label>
                <div class="col-sm-10">
                    <?= Html::textInput('to', null, [
                        'class' => 'form-control'
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">发送内容</label>
                <div class="col-sm-10">
                    <?= Html::textarea('content', null, [
                        'class' => 'form-control',
                        'rows' => 6
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">发送消息</label>
                <div class="col-sm-10">
                    <?= Html::textarea(null, null, [
                        'class' => 'form-control',
                        'rows' => 6
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">发送消息</label>
                <div class="col-sm-10">
                    <?= Html::textarea(null, null, [
                        'class' => 'form-control',
                        'disabled' => true,
                        'rows' => 10
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">接收消息</label>
                <div class="col-sm-10">
                    <?= Html::textarea(null, null, [
                        'class' => 'form-control',
                        'disabled' => true,
                        'rows' => 5
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
        </div>
    </div>
<?php
ActiveForm::end();
?>
<div class="main">
    <form action="" method="get" class="form-horizontal form" style="float:left;">
        <h4>模拟测试</h4>
        <table class="tb">
            <tr>
                <th></th>
                <td><input name="submit" type="button" onclick="submitform()" value="发送" class="btn btn-primary span2">
                </td>
            </tr>
            <tr>
                <th>公众号</th>
                <td>
                    <select name="account" id="account" class="span7">

                    </select>
                </td>
            </tr>
            <tr>
                <th>消息类型</th>
                <td>
                    <div class="radio inline"><input type="radio" name="type" value="text" id="type_text"
                                                     onclick="toggle('text')" checked="checked"/><label for="type_text">
                            &nbsp;文本</label></div>
                    <div class="radio inline"><input type="radio" name="type" value="image" id="type_image"
                                                     onclick="toggle('image')"/><label for="type_image">&nbsp;图片</label>
                    </div>
                    <div class="radio inline"><input type="radio" name="type" value="location" id="type_location"
                                                     onclick="toggle('location')"/><label for="type_location">
                            &nbsp;位置</label></div>
                    <div class="radio inline"><input type="radio" name="type" value="link" id="type_link"
                                                     onclick="toggle('link')"/><label for="type_link">&nbsp;链接</label>
                    </div>
                    <div class="radio inline"><input type="radio" name="type" value="event" id="type_event"
                                                     onclick="toggle('event')"/><label for="type_event">&nbsp;菜单</label>
                    </div>
                    <div class="radio inline"><input type="radio" name="type" value="subscribe" id="type_subscribe"
                                                     onclick="toggle('subscribe')"/><label for="type_subscribe">&nbsp;模拟关注</label>
                    </div>
                    <div class="radio inline"><input type="radio" name="type" value="unsubscribe" id="type_unsubscribe"
                                                     onclick="toggle('unsubscribe')"/><label for="type_unsubscribe">
                            &nbsp;取消关注</label></div>
                    <div class="radio inline"><input type="radio" name="type" value="other" id="type_unsubscribe"
                                                     onclick="toggle('other')"/><label for="type_unsubscribe">
                            &nbsp;其他</label></div>
                </td>
            </tr>
            <tr>
                <th>发送用户</th>
                <td>
                    <input type="text" id="fromuser" value="fromUser" class="span7"/>
                </td>
            </tr>
            <tr>
                <th>接收用户</th>
                <td>
                    <input type="text" id="touser" value="toUser" class="span7"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" id="content">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="content_text">
                        <tr>
                            <th>内容</th>
                            <td><textarea id="contentvalue" rows="5" cols="50" class="span7">测试内容</textarea></td>
                        </tr>
                    </table>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="content_image">
                        <tr>
                            <th>图片</th>
                            <td><input type="text" id="picurl" value="http://www.baidu.com/img/bdlogo.gif"
                                       class="span7"/></td>
                        </tr>
                    </table>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="content_location">
                        <tr>
                            <th>X坐标</th>
                            <td><input type="text" id="location_x" class="span7" value="23.134521"/></td>
                        </tr>
                        <tr>
                            <th>Y坐标</th>
                            <td><input type="text" id="location_y" class="span7" value="113.358803"/></td>
                        </tr>
                    </table>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="content_link">
                        <tr>
                            <th>链接</th>
                            <td><input type="text" id="url" class="span7" value="http://baidu.com"/></td>
                        </tr>
                    </table>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" id="content_event">
                        <tr>
                            <th>EventKey</th>
                            <td><input type="text" id="event_key" class="span7" value="EVENTKEY"/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th>发送消息</th>
                <td>
                    <textarea id="sendxml" rows="10" cols="50" class="span7" readonly="readonly"></textarea>
                </td>
            </tr>
            <tr>
                <th>接收消息</th>
                <td>
                    <pre id="receive" style="width:520px;"></pre>
                </td>
            </tr>
        </table>
    </form>

    <div id="demoSendBox" style="position:absolute;margin-left:700px;">
        <div class="chatPanel form" style="width:300px;">
            <h4>预览效果</h4>

            <div id="svposttext" style="text-align:left; padding-bottom:10px;display:none;">
                <img src="./resource/image/noavatar_middle.gif"
                     style="width:34px;height:34px;margin-right:6px;float:right;" class="img-rounded">

                <div id="svpostinfo" class="btn btn-success"
                     style="margin-right: 4px;float: right;max-width: 184px;text-align:left;">发送内容
                </div>
                <div style="clear:both;"></div>
            </div>

            <div class="chatItem you">
                <div id="svtext" style="text-align:left; padding-bottom:10px;display:none;">
                    <img src="./resource/image/noavatar_middle.gif"
                         style="width:34px;height:34px;margin-left:6px; float:left;" class="img-rounded">

                    <div class="btn btn-success" style="margin-left: 4px;float: left;max-width: 184px;text-align:left;">
                        回复内容
                    </div>
                    <div style="clear:both;"></div>
                </div>

                <div id="svurlbox" style="display:none;">
                    <div class="media mediaFullText">
                        <div class="mediaPanel">
                            <a href="javascript:;" id="svurl" target="_blank">
                                <div class="mediaHead"><span class="title" id="svtitle">标题</span><span
                                        class="time"><?php echo date('m月d日'); ?></span>

                                    <div class="clr"></div>
                                </div>
                                <div class="mediaImg"><img id="svpic" src="false"></div>
                                <div class="mediaContent mediaContentP"><p id="svinfo"></p></div>
                            </a>

                            <div id="svinfolist"></div>
                            <div class="mediaFooter">
                                <div class="mediaFooterbox clearfix" onclick="opensvurl();"><span
                                        class="mesgIcon right">&gt;</span>
                                    <span style="line-height:50px;" class="left">查看全文</span></div>
                                <div class="clr"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$css = <<<EOF
.checkbox-inline { padding-left: 1px; margin-left: 5px !important; }

.chatPanel .left{float:left;}
.chatPanel .right{float:right;}
.chatPanel .media a{display:block;}
.chatPanel .media{border:1px solid #cdcdcd;box-shadow:0 3px 6px #999999;-webkit-border-radius:12px;-moz-border-radius:12px;border-radius:12px;width:285px;background-color:#FFFFFF;background:-webkit-gradient(linear,left top,left bottom,from(#FFFFFF),to(#FFFFFF));background-image:-moz-linear-gradient(top,#FFFFFF 0%,#FFFFFF 100%);margin:0px auto;}
.chatPanel .media .mediaPanel{padding:0px;margin:0px;}
.chatPanel .media .mediaImg{margin:25px 15px 15px;width:255px;position:relative;}
.chatPanel .media .mediaImg .mediaImgPanel{position:relative;padding:0px;margin:0px;max-height:164px;overflow:hidden;}
.chatPanel .media .mediaImg img{/* width:100%;height:164px;position:absolute;left:0px;*/width:255px;}
.chatPanel .media .mediaImg .mediaImgFooter{position:absolute;bottom:0;height:29px;background-color:#000;background-color:rgba(0,0,0,0.4);text-shadow:none;color:#FFF;text-align:left;padding:0px 11px;line-height:29px;width:233px;}
.chatPanel .media .mediaImg .mediaImgFooter a:hover p{color:#B8B3B3;}
.chatPanel .media .mediaImg .mediaImgFooter .mesgTitleTitle{line-height:28px;color:#FFF;max-width:240px;height:26px;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;overflow:hidden;width:240px;}
.chatPanel .media .mesgIcon{display:inline-block;height:19px;width:13px;margin:8px 0px -2px 4px;}
.chatPanel .media .mediaContent{margin:0px;padding:0px;}
.chatPanel .media .mediaContent .mediaMesg{border-top:1px solid #D7D7D7;padding:10px;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgDot{display:block;position:relative;top:-3px;left:20px;height:6px;width:6px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgTitle:hover p{color:#1A1717;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgTitle a{color:#707577;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgTitle a:hover p{color:#444440;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgIcon{}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgTitle p{line-height:1.5em;max-height:45px;max-width:220px;min-width:176px;margin-top:2px;color:#5D6265;text-overflow:ellipsis;-o-text-overflow:ellipsis;overflow:hidden;text-align:left;text-overflow:ellipsis;}
.chatPanel .media .mediaContent .mediaMesg .mediaMesgIcon img{height:45px;width:45px;}
/*media mesg detail*/
.chatPanel .media .mediaHead{/*height:48px;*/padding:0px 15px 4px;border-bottom:0px solid #D3D8DC;color:#000000;font-size:20px;}
.chatPanel .media .mediaHead .title{line-height:1.2em;margin-top:22px;display:block;max-width:312px;text-align:left;/*height:25px;white-space:nowrap;text-overflow:ellipsis;-o-text-overflow:ellipsis;overflow:hidden;*/}
.chatPanel .mediaFullText .mediaImg{width:255px;padding:0;margin:0 15px;overflow:hidden;max-height:164px;}
.chatPanel .mediaFullText .mediaImg img{/*margin-top:17px;position:absolute;*/}
.chatPanel .mediaFullText .mediaContent{padding:0 0 8px;font-size:16px;line-height:1.5em;text-align:left;color:#222222;}
.chatPanel .mediaFullText .mediaContentP{margin:12px 15px 0px;}
.chatPanel .media .mediaHead .time{margin:0px;margin-top:21px;color:#8C8C8C;background:none;width:auto;font-size:12px;}
.chatPanel .media .mediaFooter{-webkit-border-radius:0px 0px 12px 12px;-moz-border-radius:0px 0px 12px 12px;border-radius:0px 0px 12px 12px;padding:0px;}
.chatPanel .media .mediaFooter a{color:#222222;font-size:16px;padding:0;}
.chatPanel .media .mediaFooter .mesgIcon{margin:15px 3px 0px 0px;}
.chatPanel .media .mediaFooterbox{border-top:1px #CCC solid;}
.chatPanel .media a:hover{cursor:pointer;}
.chatPanel .media a:hover .mesgIcon{}
.mediaContent a:hover{background-color:#F6F6F6;}
.mediaContent .last:hover{-webkit-border-radius:0px 0px 12px 12px;-moz-border-radius:0px 0px 12px 12px;border-radius:0px 0px 12px 12px;background-color:#F6F6F6;}
.mediaFullText:hover{background-color:#F6F6F6;background:-webkit-gradient(linear,left top,left bottom,from(#F6F6F6),to(#F6F6F6));background-image:-moz-linear-gradient(top,#F6F6F6 0%,#F6F6F6 100%);}
.chatItem a{text-decoration:none;}.chatItem a:hover{text-decoration:none;}.mediaFooterbox{cursor:pointer; padding:0 15px;}
#svinfolist{display:none;}#svinfolist p{border-top:1px #CCC solid; padding:4px 6px;word-break:break-all; white-space:pre; margin:2px; cursor:pointer;}
#svinfolist p img{width:50px;height:50px;}
EOF;
$js = <<<EOF

EOF;
$this->registerCss($css);
$this->registerJs($js);
?>