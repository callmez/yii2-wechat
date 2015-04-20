<?php
use yii\helpers\Html;
use callmez\wechat\models\Wechat;
use callmez\wechat\assets\AngularAsset;

AngularAsset::register($this);
$this->title = '微信模拟器';
$wechats = [];
foreach (Wechat::find()->all() as $k => $wechat) {
    $wechats[$wechat->id] = [
        'name' => $wechat->name,
        'original' => $wechat->original,
        'api' => $this->context->getApiLink($wechat, [
            'hash' => $wechat->hash,
            'token' => $wechat->token
        ])
    ];
}
?>
<div ng-app="simulatorApp">
    <div class="row" ng-controller="SimulatorController">
        <div class="col-sm-8">
            <div class="page-header"><h4><?= Html::encode($this->title) ?></h4></div>
            <?= Html::beginForm('', 'post', [
                'id' => 'wechatForm',
                'class' => 'form-horizontal',
            ]) ?>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-block btn-primary" ng-click="submitMessage()" ng-disabled="submit.disabled">{{submit.text || '发送'}}</button>
                </div>
            </div>

            <div class="form-group">
                <label for="wechat" class="col-sm-2 control-label">公 众 号</label>
                <div class="col-sm-10">
                    <select class="form-control" name="wechat" ng-model="wechat" ng-change="setToUserName(wechat)">
                        <option value="">请选择公众号</option>
                        <option ng-repeat="(key, wechat) in wechats" value="{{key}}">{{wechat.name}}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">消息类型</label>
                <div class="col-sm-10">
                    <label class="radio-inline" ng-repeat="key in notSorted(types)" ng-init="type = types[key]">
                        <input type="radio" name="msgType" value="{{key}}" ng-model="data.msgType"> {{type.label}}
                    </label>
                </div>
            </div>

            <div ng-if="targetShow('fromUserName')" class="form-group">
                <label for="fromUserName" class="col-sm-2 control-label">发送用户</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="fromUserName" placeholder="发送用户的OpenID" ng-model="data.fromUserName">
                </div>
            </div>

            <div ng-if="targetShow('toUserName')" class="form-group">
                <label for="toUserName" class="col-sm-2 control-label">接收用户</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="toUserName" placeholder="接受用户的OpenID(选中了公众号之后,可以不填写)" ng-model="data.toUserName">
                </div>
            </div>

            <div ng-if="targetShow('content')" class="form-group">
                <label for="content" class="col-sm-2 control-label">发送内容</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="content" rows="6" placeholder="发送给指定用户的内容" ng-model="data.content"></textarea>
                </div>
            </div>

            <div ng-if="targetShow('pic_url')" class="form-group">
                <label for="pic_url" class="col-sm-2 control-label">图片地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="pic_url" placeholder="填写图片地址(暂只支持网络图片)" ng-model="data.pic_url">
                </div>
            </div>

            <div ng-if="targetShow('location_x')" class="form-group">
                <label for="location_x" class="col-sm-2 control-label">X 坐 标</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="location_x" placeholder="例如: 10.000001" ng-model="data.location_x">
                </div>
            </div>

            <div ng-if="targetShow('location_y')" class="form-group">
                <label for="location_y" class="col-sm-2 control-label">Y 坐 标</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="location_y" placeholder="例如: 10.000001" ng-model="data.location_y">
                </div>
            </div>

            <div ng-if="targetShow('url')" class="form-group">
                <label for="url" class="col-sm-2 control-label">链接地址</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="url" placeholder="发送的链接地址" ng-model="data.url">
                </div>
            </div>

            <div ng-if="targetShow('event_key')" class="form-group">
                <label for="event_key" class="col-sm-2 control-label">菜单事件</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="event_key" placeholder="EventKey" ng-model="data.event_key">
                </div>
            </div>

            <div class="form-group">
                <label for="send" class="col-sm-2 control-label">发送消息</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="send" rows="8" ng-model="send" ng-disabled="data.msgType != 'other'"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="receive" class="col-sm-2 control-label">接收消息</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="receive" disabled="" rows="6" ng-model="receive"></textarea>
                </div>
            </div>

            <?= Html::endForm(); ?>
        </div>
        <div class="col-sm-4">
            <div class="page-header">
                <h4>预览效果 <small><a href="javascript:;" class="text-primary">清空记录</a></small></h4>
            </div>
            <div ng-repeat="_history in history" class="clearfix">
                <div class="{{_history.type}} clearfix">
                    <img ng-if="!isString(_history.data) && _history.data.MsgType != 'news'" class="avatar" class="img-rounded" ng-src="{{avatar}}">
                    <div ng-if="_history.data.Event" class="event {{_history.data.Event}}">{{events[_history.data.Event]}}</div>
                    <div ng-if="_history.data.MsgType == 'text'" class="content">{{_history.data.Content}}</div>
                    <ul ng-if="_history.data.MsgType == 'news'" class="news">
                        <li ng-repeat="(key, news) in _history.data.Articles.item" class="clearfix">
                            <a href="{{news.Url || 'javascrpt:;'}}" target="_blank">
                                <div ng-if="_history.data.Articles.item.length == 1" class="single">
                                    <span class="alt">{{news.Title}}</span>
                                    <span class="time">{{date}}</span>
                                    <img ng-if="news.PicUrl" ng-src="{{news.PicUrl}}" />
                                    <p class="desc">{{news.Description}}</p>
                                    <span class="link">查看全文</span>
                                </div>
                                <div ng-if="_history.data.Articles.item.length > 1" class="{{key == 0 ? 'top' : 'list'}}">
                                    <img ng-if="key > 0 && news.PicUrl" src="{{news.PicUrl}}" />
                                    <span class="alt">{{news.Title}}</span>
                                    <img ng-if="key == 0 && news.PicUrl" src="{{news.PicUrl}}" />
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div ng-if="isString(_history.data)" class="text-danger">{{_history.data}}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
angular.module('simulatorApp', [])
    .controller('SimulatorController', function($scope, $http) {
        $scope.avatar = 'data:image/jpg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMsaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjUtYzAxNCA3OS4xNTE0ODEsIDIwMTMvMDMvMTMtMTI6MDk6MTUgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2MTIxQjlGOTUwNTUxMUU0QjZFNkY3OUQyNzkxMjJCNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2MTIxQjlGQTUwNTUxMUU0QjZFNkY3OUQyNzkxMjJCNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjYxMjFCOUY3NTA1NTExRTRCNkU2Rjc5RDI3OTEyMkI2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjYxMjFCOUY4NTA1NTExRTRCNkU2Rjc5RDI3OTEyMkI2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJCwgG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';
        $scope.locationImg = 'data:image/jpg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAcEBAQFBAcFBQcKBwUHCgwJBwcJDA0LCwwLCw0RDQ0NDQ0NEQ0PEBEQDw0UFBYWFBQeHR0dHiIiIiIiIiIiIiL/2wBDAQgHBw0MDRgQEBgaFREVGiAgICAgICAgICAgICAhICAgICAgISEhICAgISEhISEhISEiIiIiIiIiIiIiIiIiIiL/wAARCABvAMgDAREAAhEBAxEB/8QAHAABAAEFAQEAAAAAAAAAAAAAAAMBAgQFBgcI/8QATBAAAAUBAwYICQkGBQUAAAAAAAECAwQRBQYSExQhIjFRBzJBUlSRktIVFyNTYXGToaMWJDM0N0JicoF0g7Gys9FDY3OCwQglNUTh/8QAGgEBAAMBAQEAAAAAAAAAAAAAAAECAwUGBP/EADARAAIBAgMHBAEEAgMAAAAAAAABAgMRExRSBBIhMVFhkQUyQXEiIzRCgTOhscHx/9oADAMBAAIRAxEAPwD3yfPzeiElVZ+4UnOxWUrGF4Wmb09QyxGU32PC03enqDEY32PC03enqDEY32PC03enqDEY32PC03enqDEY32PC03enqDEY32WuWxOS2pRGmpEZ7BWdaSi32JjK7MVF4rTNEUzNFXTIl6voHC2T1ivOpGLtZ9jpV9mhGDa+DLVbE0kmdU6C3D0CqM5m+ZVoT5DBsZMy8o3jVUuXQLzlYvJ2MbwtN3p6hniMpvsljWlKcdwqMqeoXhNtkxkTuS3k0pTSZFsGpoHJbyUGoqVIgBR6Y+hhS00qRbgBLIfcRLcaTxEkky/Wv9gBGiW8eKtNB02AC7OXfQAGcu+gAM5d9AAZy76AAzl30AC12W8ls1FSpACZl/HoPaANZav1w/ykPnqczKfMxRQoAAAAAAAAFr30K/yn/AUrex/RaHuX2YDf0UH8yf5R5T0//NH7O3tP+ORsF8RXqMevRwkbadBVIbjLJ5tujRFRz9Ng3lG5tKNzRXitSyLvR0u2lOZJbmhlholOPOHuQ2mpmKqiUlFLmc0XCZMQ9iYsKUtnnrcaZXT8hqxCYqK+T585RT9yN9d++tjW7JTByxwLT4xQ5pKaWqm3AfFX+hjU+qnUjLk7nQqhuKSZZyzp/wAwxBcKguLTgOSzp/GYAnnxFqlqcJ5tGJKSwqUaT0VAEBQnCr85Z0nXjmAK5o70ln2hgBmjvSWfaGAGaO9JZ9oYAZo70ln2hgBmjvSWfaGAKKhOKSZZyzp/GYAljx1oeJRvtrLmpWZmAIZ0TKyMeKmghnKndlJRuYLzeTcwVrQZSjZmbRYKkAAAAAAAVNvKNuFWmoo/cInG8X9MvSX5I18ZGMrPRsqpP8pjynpqvtEPs7W0+yRtsyxMmrFyGPZKicbcK3lfjRLPK0JP0EOGp5fqSVRdwuWaPHo0iRIfVakrWtWbrrX5ptWlDCOalJbd5j5a1TjurkeY9S2yU57kfairL0qSwuVCgTJkJozJyYw1iaLDxsNTJS6fhIStlkyKfo1WUblq1xLQhoSteUjr12H08ZtRbFoPalSTGalKmzClUqbNUPR7hW1Itu7RrnGjwlDWuLLMkFrLb2L/AN6TIx9/PietpVN+Kkvk30hgijqPV2cwgNBbiUnai6kR6iP+RjVM6hFCaQbh0Ii0c0jCjzIgZWbF+HsJGxqM2L8PYSAGbF+HsJADNi/D2EgBmxfh7CQBY7HLJK4vYSAJ4zJJeSej9EkQAufymU1aU9IA10vFlzxbfQMKvMylzIhmUAAAAAAAvRiwuU82rb6hPw/pmtH3I10KuKzqbcaf5THk/Sv3EPs7O0eyRvCyubHoTSit/pHtzkGv4QLOk2ldKdAjFV6RZriEl6aEejqCPMk8cakZ1CSpCsGcMYEqP7qjRg0/lUOa/wAanHqePmsOv+XwzvrpcI10rPu1BizZDVnS4DJMvQXMROYkJoZoSRa+MyqWEdLmethOLV1yODJw3JEh4mzZTMlPSWo56DbbdVVKTLk3j4NqknLgeZ9Vmp1vxO84IWX1WDalokXzefNWtgz5UtNpaxF6DNI+6CtFI9BscHGlFM7WTlc2VUk0p6RJ9Jbbf/lF/kR/yMKpnUMdh7IqNVK1KgrCViqdibwgfM94vjFt8eED5nvDGG+PCB8z3hjDfHhA+Z7wxhvjwgfM94Yw3yi5xqSacG30hjDfJokw3JCU4aVExqXJUrmBba6TzLOVNaqdRK8JdQ856ztdSG0WjJpWR09lpxcOKuYJk0rSctZn/qEOXnq2tm+BDSimBnpS/aBnautjLw0oYGelL9oGdq62MvDShgZ6Uv2gZ2rrYy8NKGBnpS/aBnautjLw0oYGelL9oGdq62MvDShhZL/2l6dH0hBnaut+SVRiv4ovU0ylpsspgS39GslUPrGMZuPFcGWauW4k7M9cpuyo+jP19cvJXBj0RdldJHnzlUlQvK7C3Bn6+uXkYMeiPPb43S8EOPWrZR5ey3VG7OipMlOMrPjPNFypP7yR1dh9RVX9Oo/y+H17M4nrHo2Kt+HM0CZ72pgcJaTIjaWVD0HswmPtcpLgePk5we7xMywLDlXjlLjNO5CzUHhnz60Mz5WWa7VH95XIMtp2qOzxu+NR8l/2zv8Ao/orm8SfI9OhRYUKG1ChyVNRGEkhppLpUJJcg4T9Rrv+bPVqjHoibEkyoc1ym7Khn6+uXknBj0RRZocVjXMcUvebpCHttbW/JGBDSimBnpS/aCM7V1sZeGleBgZ6Uv2gZ2rrYy8NKGBnpS/aBnautjLw0oYGelL9oGdq62MvDShgZ6Uv2gZ2rrYy8NKGBnpS/aBnautjLw0oYGelL9oGdq62MvDSjLsZLZWi3hfUs9Oqa68m4ff6VtNSW0RTk2uPz2MdopRUHZJG1lsw1PVdaStdNpoxe+g9RKjCT4pP+jnqbXI18mJFN48DKafkL+w+eps1O/tXgpKpLqyPM2PMp7JCmXhpXgriz6sZmx5lPZIMvDSvAxZ9WMzY8ynskGXhpXgYs+rGZseZT2SDLw0rwMWfVjM2PMp7JBl4aV4GLPqy5MWMlLhqZTxFU1OWnqDL07P8VyfwaUakt7mzBikg/B5LLEg1pqRlX7p8g8v6ar7RBPlc61d/gzblHgZufkEVor/D9foHsstS0x8I5eJLqyy8EixrHs1Vpy2EFGjQzfdwoKtE0PdtBbLS0x8IYkurOCt1Bqu+d4r3OPIiPm2UWwLNNMck5Y6NpfkaFGo66x1IiErZ6V+EY+DHNSbtc4i2brW/AttqxYlnlE8MUVY7LbqpDbRK0Opy1NOAtY9wVNnUppnyVtjU6ikdfdi7sHOZVj2Qp+794rNQhbjeWK0LPdJexRpWWjEZaS0GLVKNNu8op/0fVKu4fPA7K69rNWtZ0lE6E1HteA6qLOaQklIyiSIyWg6cVaTJRCmVpaY+EbRrSfybaRHgZuqjCCOnm/8A4GVpaY+ETiS6spbMKGi0lkllBJwJ0EkvSMauz09K8FJ1ZdWYmbRvNI7JDLAp6V4KYsurGaxvNI7JBgU9K8DFl1YzWN5pHZIMCnpXgYsurGaxvNI7JBgU9K8DFl1YzWN5pHZIMCnpXgYsurGaxvNI7JBgU9K8DFl1YzaN5pHZIMCnpXgYsurMizWGEzW1JQkj06SIi5BpRowUrpK5KqSfNs2Ty0kuhqIj9Y+wuQocRjXrFtLl9AkgvyrfOLrADKt84usAMq3zi6wAyrfOLrADKt84usAWSHG83d1i4iuX0Clb2S+mTDmjRQTIlWcZ7Maf5THi/S/3EPs620exm7S4jNj1i2K5fWPbHILbfs6HalnHZk36rLhmy6WzVVQtAlEnn77t6LAiHZVuWf4ashtGTTOjpJ7KNFoIpDB6SVQtNBVx6HPq7LNO8DnlzeCtTiVqgy2VprRpJzmiTXaSUFxf9oi8ymJtOk3NjW07m2aXLu+tKVq0vuoOKxiPRjdcd8osN2T5hUK1R/nwOzupd87Csh9MmSUq05jipM+QWglOqKlElzUpIkkLnRjGysbiS4jNlaxbN4gsS2i2wq0XMptwopp9YhxTIaMZDMQ8VTLQdC0iMNEbqLshD3l2gw0N1DIQ95doMNDdQyEPeXaDDQ3UMhD3l2gw0N1DIQ95doMNDdRa6zFJszIyr6ww0N1E0ZqOl5Jopi9YKCQsSPqaJzWNNfTQWLEKHGca9ZO3eW4CC/KM85HWQkDKM85HWQAZRnnI6yADKM85HWQAZRnnI6yAEchbObuayeIrlLcKVvY/pkw5o0cIyJVnV2Y07fymPF+lfuIfZ1to9jN2lbObHrJ2K5S9I9scgyJSkEuPiMvq5bf0AkhJxnLHrJ2FylvEkBbjGVQZmjl3bgBV11oyTrJ4xcpbwBR1bGSVrJ2HykALZK2c2XrJ2byEEmTOU2Vou4jItVG2npAEDa2dbWTxj5SEkF+UZ5yOsgAyjPOR1kAGUZ5yOsgAyjPOR1kAGUZ5yOsgBY8tnJK1k7N5ACVlTRuJwmmvJSggF7z2BWltgy5zu0CSFM5szMsEPR6gBdnjfMh+4AM8b5kP3ABnjfMh+4AM8b5kP3ABnjfMh+4ARyZjebOasTiK2UrsFKvsf0yYc0aCC80SrO1m1UWnQpRU4p7R430uLzEPs6u0P8GdCVoN5PHgicuipcg9qckmlWiyZskko6sTeLyhkdPQQAhKc3jNOCH7gBLGmRlSUtuIjElRHpThroAGvXeJklqIo8WhGZbS5DH0R2a6vcwlXs+RT5RtdHi9ZC2U7kZnsCvI1Uvm8XaXKQrLZrLmTGvd8jLte3GGJmTJuO7qkeNZlXSKUqW8XqVd0xPlG10eL1kNcp3M8z2Hyka6PF6yDKdxmew+UjXR4vWQZTuMz2Hyka6PF6yDKdxmew+UjXR4vWQZTuMz2Hyka6PF6yDKdxmew+UbXR4vWQZTuMz2JoNspkyEtJYYTX77dKlyik9n3Ve5aFa7PFuHha/GE63jPBmkbUqeHYfJsGBscLhTuAgYU7gAwp3ABhTuADCncAGEtwAYS3ABhTuADCncAGFO4AMKdwAYU7gAwluADCW4AMJbgAwluADCW4AKFuAChbgAoW4AKFuAChbgAoW4AdZwNmZcJdlJIzJJ5apFsPyC9osuTI+TN4ePtEd/ZI38DFSxw4EAAAAAEkVLapTSHCM21OJSsiOhmRqIjofIIZJ7hI4ILmst2YRWUtx1t3JvFnVDXic2vKweUwkR7Kbh8WYlxLWMS8XBxciPYFvSoVlIVIaaU/GwyHKoJFTM0ktCUoIuZU67KiY1pXQsY1mXP4Nn7rRbQZs553HZb8nLyNGLJuISpS1IPQ6VTw0+6JnUne3cWNJfm4Nn2Ndq2X4UA8ce2EtsyNZSmoWQQvSoz4prWRVMaU6t5Ihllg3Y4O5FwItoz5LqZS7SZYfkpjmpwnFJKsQtNDbPnhKct7+gjobXudwWsNS45wpJf96Zs81MmhtTTjyE0Q2o8VWSrU66ajNTn/omxpIt0btwbDvMiRYzloy7FmZrGm5R5BupdXgxYW9XyBaTp+tBeVR3XG1yDq59zOD+Gm1kpgWQnwcljCb5yKoylKnKw86urh/UZKrJ25k2NL4u7AZvrb1pLhNKuzY8NL2Y1UaVvrjE7RNTxYS0mNMZ7q6sg0toxuC67yjjWlZFpLdnxW32jU+w5km3dZK2zThwr0ctRZOb5WBsLEuddNm98myvBxrpAbcbYtB9h0yeeViStBEtgllky0lXQInUlu3B1zfBxdHIpkLsOIaUFk3WcKCNbh/4iV5waUp/BtGGNLqTY8+4WLAsWyYlmIhQ48SYanc6UwbaTWk6YPJJdfNJFp0mY+ihJshnBj6CoAAAAAAHV8Dn2mWT+/8A6CxZciPkzeHj7RHf2SN/AxUscOBAAAAABNZ7S3bQjNI0rW82lJek1kIfIk+mLUtBDkZuREUbpok6pRzaWo8DiyOmUUhFNGnSOYkXuai91uWPLurbUay5qZMpmK63MbjKaWtFWzMzMlrIqEW3BUWpwaauGzVsyrSl3fYu2aY0efIu9LUdlRlNpbJ5akJZppoRmkz5d40as79yDCjOuIuiXB5bU1C70WnCfdVlXUrybqTTm0dblTKpoRv5Bb+W+uRBorMuzbTXBK0txlKUotdm01eUb0RG0ES3ONyU2bReU1v/ANA62ZYk+RaM3HAflWc9bLVsRpMN6LRaGmEkgvKOJPS4nTo2DLe/4sSaWMu9MywL4pRIRAmzbQrEgnLbStujnzlBaaVWnRo4wu7Xj82IOusxV4kWLOQ6xaqZCUtFGQ7MgreVRWtkVpLCkyLjZTaQxdr/APpJw8Rucm+96kzikNPPWDIVgmPNPO1NCEkZqa8nyaCLYQ3l7V9kGNfO4N7LwSbNlR4SWjYgR4klK5UXjs1KrdF6SMj5RMKqjcWOksOz7RVwzOWk7FyMRFlk22a3WVqwpImkqVk1KIjWaT0EYzlL9MfJ2pLXzT9o7/cfMWPLP+oSK8c2x5uDyObrZNda6+PFhOuts06R9uyPgysjzAfSVAAAAAAA6vgc+0yyf3/9BYsuRHyZvDx9orv7JG/gYqWOHAgAAAAAAClCAChACtABShACoApQgANKdwArQgJAAphIAVoBAoBIAACAAAAAAAOr4HPtMsn9/wD0Fiy5EfJ6Nws8GMu8rrdr2SpPhFlGTeaWeEnEFpTQ+QyFSx5efBrfUlGWY7P85nvgCni2vp0H4rPfADxbX06D8VnvgB4tr6dB+Kz3wA8W19Og/FZ74AeLa+nQfis98AFcG19cJ/MfjM98GCJPBtfrU+Y+vyzHfHy01Pe4mkrEvi2vrT6j8Znvj6jMqfBvfTV+Y8mnyrPfAFPFtfWv1H4rHfAFU8G99MZVg6P9VjvgCE+DW/VfqPxmO+LqxVlPFrfroPxme+J4EDxa366D8ZjviHYkkf4Nr7ZTUg6KeeY74iJLI/FrfroPxme+LcCo8Wt+ug/GZ74cAPFrfroPxme+HADxa366D8ZnvhwA8Wt+ug/GZ74cAPFrfroPxme+HAFfFrfroPxme+HAHo3BBwVWpYlpfKC3MKZCUGiJHSol0xlRS1GWjZooKtkpH//Z';
        $scope.wechats = <?= json_encode($wechats, JSON_UNESCAPED_UNICODE) ?>;
        $scope.types = {
            text: {
                label: "文本",
                target: ["fromUserName","toUserName", "content"]
            },
            image: {
                label: "图片",
                target: ["pic_url"]
            },
            location: {
                label: "位置",
                target: ["location_x", "location_y"]
            },
            link: {
                label: "链接",
                target: ["url"]
            },
            event: {
                label: "菜单",
                target: ["event_key"]
            },
            subscribe: {
                label: "关注",
                target: ["fromUserName", "toUserName"]
            },
            unsubscribe: {
                label: "取消关注",
                target: ["fromUserName", "toUserName"]
            },
            other: {
                label: "其他"
            }
        };
        $scope.events = {
            subscribe: '关注事件',
            unsubscribe: '取消关注事件',
            SCAN: '扫码事件',
            CLICK: '自定义菜单事件',
            VIEW: '点击菜单跳转链接时事件',
            LOCATION: '上报地理位置事件'
        };
        var d = new Date();
        $scope.date = d.getMonth()+1 + '月' + d.getDay() + '日';
        $scope.history = [];
        $scope.data = {
            msgType: 'text' // 默认为文本类型
        };

        // 提交数据
        $scope.submitMessage = function() {
            if (!$scope.wechat)  {
                alert('请先选择公众号');
            } else if (!$scope.data.msgType) {
                alert('请先选择消息类型');
            } else if(!$scope.data.fromUserName) {
                alert('发送用户必填');
            } else if (!$scope.data.toUserName) {
                alert('接收用户必填');
            } else {
                $scope.addHistory($scope.generateData(), 'send');
                $scope.submit = {
                    disabled: true,
                    text: '提交中...'
                };
                $http({
                    url: $scope.wechats[$scope.wechat].api,
                    data: $scope.send.replace(/[\r\n]/g, ''),
                    method: 'post',
                    headers: {"Content-type": "text/xml"}
                }).success(function(response) {
                    $scope.submit = {
                        disabled: false
                    };
                    if (!response) {
                        return;
                    }
                    $scope.receive = response;
                    var xml = angular.fromJson(xml2json(parseXml(response), ''));
                    $scope.addHistory(xml.xml, 'receive');
                }).error(function(data, status, headers, config) {
                    $scope.submit = {
                        disabled: false
                    };
                    $scope.addHistory('请求失败:' + status, 'eroor')
                });
            }
        };

        // 记录发送历史
        $scope.addHistory = function(data, type) {
            if (data.hasOwnProperty('Articles') && data.Articles.item.hasOwnProperty('Title')) {
                data.Articles.item = [data.Articles.item];
            }

            $scope.history.push({
                type: type,
                data: data
            });
        }

        // 生成数据
        $scope.generateData = function() {
            var _data = {};
            switch ($scope.data.msgType) {
                case 'text':
                    _data = {
                        msgType: 'text',
                        content: ''
                    };
                    break;
                case 'image':
                    _data = {
                        picUrl: ''
                    };
                    break;
                case 'location':
                    _data = {
                        location_X: '',
                        location_Y: '',
                        scale: 20,
                        label: '位置信息'
                    };
                    break;
                case 'link':
                    _data = {
                        title: '测试链接',
                        description: '测试链接描述',
                        url: ''
                    };
                    break;
                case 'subscribe':
                case 'unsubscribe':
                    _data = {
                        msgType: 'event',
                        event: $scope.data.msgType
                    };
                    break;
                case 'event':
                    _data = {
                        event: 'CLICK',
                        eventKey: ''
                    };
                    break;
            }
            _data = angular.extend({
                fromUserName: '',
                toUserName: '',
                msgType: '',
                msgId: 1234567890123456,
                createTime: Math.round(new Date().getTime()/1000)
            }, _data);

            var _return = {};
            angular.forEach(_data, function (value, key) {
                if (value == '' && $scope.data.hasOwnProperty(key)) { // 如果_data有空值代表此值可以被覆盖, 否则只能取设定的值
                    value = $scope.data[key];
                }
                this[key.substring(0, 1).toUpperCase() + key.substring(1)] = value;
            }, _return);
            return _return;
        };

        // 监控数据改变
        $scope.$watch('data', function (newValue, oldValue, scope) {
            $scope.send = $scope.data.msgType == 'other' ? '' : "<xml>\n" + json2xml($scope.generateData()) + "\n</xml>"; //生成要发送的数据
        }, true);

        // 公众号选取设置
        $scope.setToUserName = function(wechat) {
            if ($scope.wechats[wechat]) {
                $scope.data.toUserName = $scope.wechats[wechat].original;
            }
        }

        // 消息类型相关表单显示
        $scope.targetShow = function(target) {
            return $.inArray(target, $scope.types[$scope.data.msgType].target) >= 0;
        }

        //ngRepeat排序
        $scope.notSorted = function(obj){
            if (!obj) {
                return [];
            }
            return Object.keys(obj);
        }
        $scope.isString = angular.isString;
    });

    function parseXml(xml) {
        var dom = null;
        if (window.DOMParser) {
            try {
                dom = (new DOMParser()).parseFromString(xml, "text/xml");
            }
            catch (e) { dom = null; }
        }
        else if (window.ActiveXObject) {
            try {
                dom = new ActiveXObject('Microsoft.XMLDOM');
                dom.async = false;
                if (!dom.loadXML(xml)) // parse error ..

                    window.alert(dom.parseError.reason + dom.parseError.srcText);
            }
            catch (e) { dom = null; }
        }
        else
            alert("无法解析xml数据!");
        return dom;
    }
    /*	This work is licensed under Creative Commons GNU LGPL License.

     License: http://creativecommons.org/licenses/LGPL/2.1/
     Version: 0.9
     Author:  Stefan Goessner/2006
     Web:     http://goessner.net/
     */
    function json2xml(o, tab) {
        var toXml = function(v, name, ind) {
            var xml = "";
            if (v instanceof Array) {
                for (var i=0, n=v.length; i<n; i++)
                    xml += ind + toXml(v[i], name, ind+"\t") + "\n";
            }
            else if (typeof(v) == "object") {
                var hasChild = false;
                xml += ind + "<" + name;
                for (var m in v) {
                    if (m.charAt(0) == "@")
                        xml += " " + m.substr(1) + "=\"" + v[m].toString() + "\"";
                    else
                        hasChild = true;
                }
                xml += hasChild ? ">" : "/>";
                if (hasChild) {
                    for (var m in v) {
                        if (m == "#text")
                            xml += v[m];
                        else if (m == "#cdata")
                            xml += "<![CDATA[" + v[m] + "]]>";
                        else if (m.charAt(0) != "@")
                            xml += toXml(v[m], m, ind+"\t");
                    }
                    xml += (xml.charAt(xml.length-1)=="\n"?ind:"") + "</" + name + ">";
                }
            }
            else {
                xml += ind + "<" + name + ">" + v.toString() +  "</" + name + ">";
            }
            return xml;
        }, xml="";
        for (var m in o)
            xml += toXml(o[m], m, "");
        return tab ? xml.replace(/\t/g, tab) : xml.replace(/\t|\n/g, "");
    }
    /*	This work is licensed under Creative Commons GNU LGPL License.

     License: http://creativecommons.org/licenses/LGPL/2.1/
     Version: 0.9
     Author:  Stefan Goessner/2006
     Web:     http://goessner.net/
     */
    function xml2json(xml, tab) {
        var X = {
            toObj: function(xml) {
                var o = {};
                if (xml.nodeType==1) {   // element node ..
                    if (xml.attributes.length)   // element with attributes  ..
                        for (var i=0; i<xml.attributes.length; i++)
                            o["@"+xml.attributes[i].nodeName] = (xml.attributes[i].nodeValue||"").toString();
                    if (xml.firstChild) { // element has child nodes ..
                        var textChild=0, cdataChild=0, hasElementChild=false;
                        for (var n=xml.firstChild; n; n=n.nextSibling) {
                            if (n.nodeType==1) hasElementChild = true;
                            else if (n.nodeType==3 && n.nodeValue.match(/[^ \f\n\r\t\v]/)) textChild++; // non-whitespace text
                            else if (n.nodeType==4) cdataChild++; // cdata section node
                        }
                        if (hasElementChild) {
                            if (textChild < 2 && cdataChild < 2) { // structured element with evtl. a single text or/and cdata node ..
                                X.removeWhite(xml);
                                for (var n=xml.firstChild; n; n=n.nextSibling) {
                                    if (n.nodeType == 3)  // text node
                                        o["#text"] = X.escape(n.nodeValue);
                                    else if (n.nodeType == 4)  // cdata node
                                        o["#cdata"] = X.escape(n.nodeValue);
                                    else if (o[n.nodeName]) {  // multiple occurence of element ..
                                        if (o[n.nodeName] instanceof Array)
                                            o[n.nodeName][o[n.nodeName].length] = X.toObj(n);
                                        else
                                            o[n.nodeName] = [o[n.nodeName], X.toObj(n)];
                                    }
                                    else  // first occurence of element..
                                        o[n.nodeName] = X.toObj(n);
                                }
                            }
                            else { // mixed content
                                if (!xml.attributes.length)
                                    o = X.escape(X.innerXml(xml));
                                else
                                    o["#text"] = X.escape(X.innerXml(xml));
                            }
                        }
                        else if (textChild) { // pure text
                            if (!xml.attributes.length)
                                o = X.escape(X.innerXml(xml));
                            else
                                o["#text"] = X.escape(X.innerXml(xml));
                        }
                        else if (cdataChild) { // cdata
                            if (cdataChild > 1)
                                o = X.escape(X.innerXml(xml));
                            else
                                for (var n=xml.firstChild; n; n=n.nextSibling)
                                    o["#cdata"] = X.escape(n.nodeValue);
                        }
                    }
                    if (!xml.attributes.length && !xml.firstChild) o = null;
                }
                else if (xml.nodeType==9) { // document.node
                    o = X.toObj(xml.documentElement);
                }
                else
                    alert("unhandled node type: " + xml.nodeType);
                return o;
            },
            toJson: function(o, name, ind) {
                var json = name ? ("\""+name+"\"") : "";
                if (o instanceof Array) {
                    for (var i=0,n=o.length; i<n; i++)
                        o[i] = X.toJson(o[i], "", ind+"\t");
                    json += (name?":[":"[") + (o.length > 1 ? ("\n"+ind+"\t"+o.join(",\n"+ind+"\t")+"\n"+ind) : o.join("")) + "]";
                }
                else if (o == null)
                    json += (name&&":") + "null";
                else if (typeof(o) == "object") {
                    var arr = [];
                    for (var m in o)
                        arr[arr.length] = X.toJson(o[m], m, ind+"\t");
                    json += (name?":{":"{") + (arr.length > 1 ? ("\n"+ind+"\t"+arr.join(",\n"+ind+"\t")+"\n"+ind) : arr.join("")) + "}";
                }
                else if (typeof(o) == "string")
                    json += (name&&":") + "\"" + o.toString() + "\"";
                else
                    json += (name&&":") + o.toString();
                return json;
            },
            innerXml: function(node) {
                var s = ""
                if ("innerHTML" in node)
                    s = node.innerHTML;
                else {
                    var asXml = function(n) {
                        var s = "";
                        if (n.nodeType == 1) {
                            s += "<" + n.nodeName;
                            for (var i=0; i<n.attributes.length;i++)
                                s += " " + n.attributes[i].nodeName + "=\"" + (n.attributes[i].nodeValue||"").toString() + "\"";
                            if (n.firstChild) {
                                s += ">";
                                for (var c=n.firstChild; c; c=c.nextSibling)
                                    s += asXml(c);
                                s += "</"+n.nodeName+">";
                            }
                            else
                                s += "/>";
                        }
                        else if (n.nodeType == 3)
                            s += n.nodeValue;
                        else if (n.nodeType == 4)
                            s += "<![CDATA[" + n.nodeValue + "]]>";
                        return s;
                    };
                    for (var c=node.firstChild; c; c=c.nextSibling)
                        s += asXml(c);
                }
                return s;
            },
            escape: function(txt) {
                return txt.replace(/[\\]/g, "\\\\")
                    .replace(/[\"]/g, '\\"')
                    .replace(/[\n]/g, '\\n')
                    .replace(/[\r]/g, '\\r');
            },
            removeWhite: function(e) {
                e.normalize();
                for (var n = e.firstChild; n; ) {
                    if (n.nodeType == 3) {  // text node
                        if (!n.nodeValue.match(/[^ \f\n\r\t\v]/)) { // pure whitespace text node
                            var nxt = n.nextSibling;
                            e.removeChild(n);
                            n = nxt;
                        }
                        else
                            n = n.nextSibling;
                    }
                    else if (n.nodeType == 1) {  // element node
                        X.removeWhite(n);
                        n = n.nextSibling;
                    }
                    else                      // any other node
                        n = n.nextSibling;
                }
                return e;
            }
        };
        if (xml.nodeType == 9) // document node
            xml = xml.documentElement;
        var json = X.toJson(X.toObj(X.removeWhite(xml)), xml.nodeName, "\t");
        return "{\n" + tab + (tab ? json.replace(/\t/g, tab) : json.replace(/\t|\n/g, "")) + "\n}";
    }
</script>
<?php
$this->registerCss("
.avatar { width: 34px; height: 34px; border-radius: 3px; }
.content, .event { max-width: 218px; font-size: 14px; line-height: 1.42857143; border: 1px solid transparent; border-radius: 3px; padding: 6px 12px; }
.send, .receive { margin-bottom: 15px; }
.send { text-align: right; }
.send .avatar, .send .event, .send .content { float: right; color: #fff; }
.send .event, .send .content { margin-right: 7px; }
.send .event { background-color: #d95c5c; }
.send .content { background-color: #5cb85c; border-color: #4cae4c; }
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
");