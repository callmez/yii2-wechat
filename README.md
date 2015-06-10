
Yii-wechat
==========

感谢选择 Yii-wechat，基于 [Yii2](https://github.com/yiisoft/yii2) 框架基础实现的模块。

[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads)](https://packagist.org/packages/overtrue/wechat)

注意
----
  - 如果是全新使用`Yii2`和`Yii2-wechat`,你可以使用 [Yii2-app-wechat](https://github.com/callmez/yii2-app-wechat) 微信应用模板(可在该模板基础上开发).
  - 如果是已有的`Yii2`项目扩展`Yii2-wechat`, 请遵循下面的安装步骤使用.
  - 如果想深度二次开发`Yii2-wechat`模块, 只需下载代码放到项目的`modules`目录中, 并把`Yii2-wechat`中`composer.json`的`require`, `require-dev`, `autoload` 三个节点(没有的节点可忽略)的内容**合并**到您的项目`composer.json`中, 并在项目目录下执行`composer update`命令. 该实现需要一定的PHP功底, 并且会放弃后期的版本升级功能.
  
  
  - **另本项目仍在开发阶段, 很多功能仍需思考, 建议仅用于`Yii2`和`wechat`功能学习**
  

环境条件
-------

- >= php5.4
- >= Yii2

特点
----
  - [x] 多公众号管理
  - [ ] 企业号支持?
  - [ ] 消息回复
    - [ ] 文本回复
    - [ ] 图文回复
    - [ ] 音乐回复
    - [ ] 语音回复
    - [ ] 视频回复
    - [ ] 图片回复
    - [ ] 远程回复
  - [ ] 素材管理
  - [x] 自定义菜单
  - [ ] 二维码管理
  - [ ] 卡券功能
  - [ ] 多客服
  - [ ] 粉丝
    - [ ] 粉丝管理
    - [ ] 粉丝分组
    - [ ] 粉丝互动
  - [ ] 消息
    - [ ] 历史记录
    - [ ] 普通(微信)群发
  - [ ] 支付
    - [ ] 微信支付
    - [ ] 支付宝
  - [ ] 插件
    - [x] 模块扩展平台
    - [ ] 基本模块
  - [ ] 开发支持
    - [x] 微信模拟器
    - [ ] 开发文档
  - [ ] 待定功能

  **想提新功能?** 提交[issue](https://github.com/callmez/yii2-wechat/issues)
安装
---

安装步骤如下(2种方式)：

1. 通过composer.json文件安装
   - `cd 项目目录 && composer require callmez/yii2-wechat`

   或者

   - 项目目录下的composer.json
   - 添加`"callmez/yii2-wechat": "dev-master"`内容,然后执行`composer update` (模块中使用了angular的bower源,请确定使用[composer-asset-plugin](https://github.com/francoispluchino/composer-asset-plugin) **大于** `beta4`的版本)
    ```
    "require": {
        ...
        "callmez/yii2-wechat": "*",
        ...
    }
    ```
    
### 安装完后, 在`config/web.php` 文件中配置`module`配置和`components`配置(`...`号代表其他设置)

```php
  ...
  'modules' => [
    ...
    'wechat' => [ // 指定微信模块
        'class' => 'callmez\wechat\Module',
        'adminId' => 1 // 填写管理员ID, 该设置的用户将会拥有wechat最高权限, 如多个请填写数组 [1, 2]
    ]
    ...
  ],
  'components' => [
    ...
    'request' => [
          ...
          'parsers' => [ // 因为模块中有使用angular.js  所以该设置是为正常解析angular提交post数据
              ...
              'application/json' => 'yii\web\JsonParser'
          ]
      ],
    ...
  ]
  ...
```

3. 最后生成数据库表(请确定数据库连接正常)

  执行命令 `php yii migrate --migrationPath=@callmez/wechat/migrations` 根据提示安装数据库即可

反馈或贡献代码
------------
您可以在[这里](https://github.com/callmez/yii2-wechat/issues)给我们提出在使用中碰到的问题或Bug。

你也可以发送邮件**callme-z@qq.com**说明您的问题。

交流QQ群: `343188481` (注明企图)

如果你有更好代码实现,请 fork 此项目并发起您的 Pull-Request，我会及时处理。感谢!
