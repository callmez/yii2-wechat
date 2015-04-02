
Yii-wechat
==========

感谢选择 Yii-wechat，基于 [Yii2](https://github.com/yiisoft/yii2) 框架基础实现的模块。
请参考 [Yii2-app-wechat](https://github.com/callmez/yii2-app-wechat)微信应用模板(可生成使用)了解本模块的相关功能

环境条件
-------
简而言之：

- >= php5.4
- >= Yii2

特点
---
  - 内置微信模拟器, 支持常用微信请求模拟

![default](https://cloud.githubusercontent.com/assets/1625891/4747720/f8927018-5a60-11e4-8e07-d4415f798426.png)

  - 微信多公众号后台管理

![admin](https://cloud.githubusercontent.com/assets/1625891/5060399/706aa818-6d8e-11e4-8423-ccfe01330293.png)
![admin－wechat](https://cloud.githubusercontent.com/assets/1625891/5060522/1da613f8-6d96-11e4-8653-2b544cac952a.jpg)

安装
---

安装步骤如下(2种方式)：

1. 通过composer.json文件安装
   - `cd 你的项目目录 && composer require callmez/yii2-wechat

   或者

   - 项目目录下的composer.json
   - 添加`"callmez/yii2-wechat": "dev-master"`内容,然后执行`composer update` (模块中使用了angular的bower源,请确定使用[composer-asset-plugin](https://github.com/francoispluchino/composer-asset-plugin) **大于** `beta4`的版本)
    ```
    "require": {
        ...
        "callmez/yii2-wechat": "dev-master",
        ...
    }
    ```
    
2. 适合深度定制 在命令行界面下 进入`modules`文件夹执行命令`git clone https://github.com/callmez/yii2-wechat.git` 并在`composer.json`中声明命名空间路径
  ```json
    ...
    "autoload": {
        ...
        "psr-4": {"callmez\\wechat\\": "modules/wechat"}
        ...
    },
    ...
  ```

### 安装完后, 在`config/web.php` 文件中配置`module`配置和`components`配置(`...`号代表其他设置)

```php
  ...
  'modules' => [
    ...
    'wechat' => [ // 指定微信模块
        'class' => 'callmez\wechat\Module',
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

注意
----
产品目前还是处于刚开发阶段. 后期将会着重于功能的完善和细化.

反馈或贡献代码
------------
您可以在[这里](https://github.com/callmez/yii2-wechat/issues)给我们提出在使用中碰到的问题或Bug。

你也可以发送邮件**callme-z@qq.com**说明您的问题。

如果你有更好代码实现,请 fork 此项目并发起您的 Pull-Request，我们会及时处理。感谢!

捐助作者 
-------

### 如果你觉得该程序对你有帮助,请慷慨捐助一点给作者. 使用手机浏览器或支付宝客户端扫描下面二维码.感谢支持!

> ![1414029434535](https://cloud.githubusercontent.com/assets/1625891/4747223/85530962-5a58-11e4-8665-f408c9783dd0.jpg)
