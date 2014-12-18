<?php
namespace callmez\wechat\generators\module;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;
use yii\base\InvalidConfigException;
use callmez\wechat\Module;
use callmez\wechat\models\Module as WechatModule;

class Generator extends \yii\gii\Generator
{
    public $module;
    public $moduleName;
    public $moduleDescription;
    public $version = '1.0';
    public $type;
    public $author;
    public $link;
    public $services = ['mobile', 'processor'];

    /**
     * 服务的组件
     * @var array
     */
    public static $serviceTypes = [
        'mobile' => '移动页面服务',
        'processor' => '微信消息服务',
        'receiver' => '微信订阅服务'
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
        $moduleNamespace = Module::getWechatModuleNamespace($this->module);
        $params = [
            'moduleNamespace' => $moduleNamespace
        ];

        $files[] = new CodeFile(
            Yii::getAlias('@' . str_replace('\\', '/', $moduleNamespace)) . '/info.yml',
            $this->render('info.yml.php', [
                'attributes' => $this->getYmlSettings()
            ])
        );

        $files[] = new CodeFile(
            Yii::getAlias('@' . str_replace('\\', '/', $moduleNamespace)) . '/Installer.php',
            $this->render('installer.php', $params)
        );

        $files[] = new CodeFile(
            Yii::getAlias('@' . str_replace('\\', '/', $moduleNamespace)) . '/Module.php',
            $this->render('module.php', $params)
        );

        foreach ($this->services as $k => $name) {
            $name === 'mobile' && $name = 'default';
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $moduleNamespace)) . '/controllers/'. ucfirst($name) . 'Controller.php',
                $this->render("controllers/{$name}Controller.php", $params)
            );
        }
        return $files;
    }

    public function getYmlSettings()
    {
        return [
            'module' => $this->module,
            'version' => $this->version,
            'name' => $this->moduleName,
            'description' => $this->moduleDescription,
            'type' => $this->type,
            'autor' => $this->author,
            'link' => $this->link,
            'services' => $this->services
        ];
    }

    public function rules()
    {
        return [
            [['module', 'moduleName', 'type', 'version', 'author', 'services'], 'required'],
            [['module'], 'match', 'pattern' => '/^[a-z]+[a-zA-Z\d]*$/', 'message' => '模块名必须为英文字符数字,且必须是小写英文字符开头'],
            [['moduleName'], 'string', 'length' => [2, 10]],
            [['type'], 'in', 'range' => array_keys(WechatModule::$types)],
            [['services'], 'in', 'allowArray' => true, 'range' => array_keys(self::$serviceTypes)]
        ];
    }

    public function stickyAttributes()
    {
        return ['version'];
    }

    public function attributeLabels()
    {
        return [
            'module' => '模块名称',
            'moduleName' => '模块显示名称',
            'moduleDescription' => '模块描述',
            'version' => '版本',
            'type' => '插件类型',
            'ability' => '功能',
            'author' => '作者',
            'link' => '链接',
            'services' => '服务组件'
        ];
    }

    public function hints()
    {
        return array_merge(parent::hints(), [
            'module' => '微信模块名称, 唯一模块标识,必须与模块目录命名一直. 如: <code>test</code>',
            'moduleName' => '微信模块显示名称',
            'moduleDescription' => '微信扩展描述, 请简单描述该模块提供的微信功能',
            'version' => '模块版本, 默认为1.0, 可用作插件升级标识',
            'type' => '模块的类型.用来标记模块提供的微信功能区间',
            'ability' => '模块提供的功能,简单描述即可',
            'author' => '模块开发者姓名或昵称',
            'link' => '模块的详细链接',
            'services' => '选择提供的服务组件'
        ]);
    }
}