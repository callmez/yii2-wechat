<?php
namespace callmez\wechat\generators\module;

use callmez\wechat\helpers\WechatHelper;
use Yii;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;
use yii\base\InvalidConfigException;
use callmez\wechat\helpers\PathHelper;
use callmez\wechat\components\Receiver;

class Generator extends \yii\gii\Generator
{
    public $moduleName;
    public $moduleDescription;
    public $identifier;
    public $version = '1.0';
    public $type;
    public $ability;
    public $author;
    public $url;
    public $services = ['processor', 'receiver', 'mobile'];

    /**
     * 服务的组件
     * @var array
     */
    public static $serviceTypes = [
        'processor' => '微信消息服务',
        'receiver' => '微信订阅服务',
        'mobile' => '移动页面服务'
    ];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return '微信扩展模块生成器';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return '使用该生成器可以生成一个微信的扩展功能模块.从而实现微信消息服务, 微信订阅服务, 微信网页等服务';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $moduleSpace = WechatHelper::getModuleNamespace($this->identifier);
        foreach ($this->services as $k => $name) {
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $moduleSpace)) . '.php',
                $this->render("{$name}Controller.php")
            );
        }
        return $files;
    }

    public function rules()
    {
        return [
            [['moduleName', 'identifier', 'type', 'version', 'author', 'services'], 'required']
        ];
    }

    public function stickyAttributes()
    {
        return ['version'];
    }

    public function attributeLabels()
    {
        return [
            'moduleName' => '模块名称',
            'moduleDescription' => '模块描述',
            'identifier' => '模块唯一标识符',
            'version' => '版本',
            'type' => '插件类型',
            'ability' => '功能',
            'author' => '作者',
            'url' => '链接',
            'services' => '服务组件'
        ];
    }

    public function hints()
    {
        return array_merge(parent::hints(), [
            'moduleName' => '微信扩展模块名称',
            'moduleDescription' => '微信扩展描述, 请简单描述该模块提供的微信功能',
            'identifier' => '模块同一标识符, 开发微信服务作为重要的检索查询标记. 如: <code>test</code>',
            'version' => '模块版本, 默认为1.0, 可用作插件升级标识',
            'type' => '模块的类型.用来标记模块提供的微信功能区间',
            'ability' => '模块提供的功能,简单描述即可',
            'author' => '模块开发者姓名或昵称',
            'url' => '模块的详细链接',
            'services' => '选择提供的服务组件'
        ]);
    }

    public function getServiceMobileFile()
    {

    }
}