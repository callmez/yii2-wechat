<?php
namespace callmez\wechat\generators\module;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;
use yii\base\InvalidConfigException;
use callmez\wechat\models\Module as ModuleModel;


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
        $moduleNamespace = $this->getModuleNamespace($this->module);
        $moduleBasePath = Yii::getAlias('@' . str_replace('\\', '/', $moduleNamespace));
        $params = [
            'moduleNamespace' => $moduleNamespace
        ];

        $files[] = new CodeFile(
            $moduleBasePath . '/wechat.yml',
            $this->render('wechat.yml.php', [
                'attributes' => $this->getYamlSettings()
            ])
        );

        $files[] = new CodeFile(
            $moduleBasePath . '/Installer.php',
            $this->render('installer.php', $params)
        );

        $files[] = new CodeFile(
            $moduleBasePath . '/Module.php',
            $this->render('module.php', $params)
        );

        foreach ($this->services as $k => $name) {
            $name === 'mobile' && $name = 'default';
            $files[] = new CodeFile(
                $moduleBasePath . '/controllers/'. ucfirst($name) . 'Controller.php',
                $this->render("controllers/{$name}Controller.php", $params)
            );
        }
        return $files;
    }

    public function getYamlSettings()
    {
        return [
            'module' => $this->module,
            'version' => $this->version,
            'name' => $this->moduleName,
            'description' => $this->moduleDescription,
            'type' => $this->type,
            'author' => $this->author,
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
            [['type'], 'in', 'range' => array_keys(ModuleModel::$types)],
            [['services'], 'in', 'allowArray' => true, 'range' => array_keys(ModuleModel::$serviceTypes)],
            [['moduleDescription', 'link'], 'safe']
        ];
    }

    public function stickyAttributes()
    {
        return ['version'];
    }

    public function attributeLabels()
    {
        return [
            'module' => '模块标识',
            'moduleName' => '模块名称',
            'moduleDescription' => '模块描述',
            'version' => '版本',
            'type' => '插件类型',
            'author' => '作者',
            'link' => '链接',
            'services' => '服务组件'
        ];
    }

    public function hints()
    {
        return array_merge(parent::hints(), [
            'module' => '微信模块名称, 唯一模块标识,必须与模块目录命名一直 如: <code>test</code>',
            'moduleName' => '微信模块显示名称 如: <code>测试模块</code>',
            'moduleDescription' => '微信扩展描述, 请简单描述该模块提供的微信功能',
            'version' => '模块版本, 默认为1.0, 可用作插件升级标识 如: <code>1.0</code>',
            'type' => '模块的类型.用来标记模块提供的微信功能区间',
            'author' => '模块开发者姓名或昵称 如: <code>CallMeZ</code>',
            'link' => '模块的详细链接 如: <code>http://domain</code>',
            'services' => '选择提供的服务组件'
        ]);
    }

    /**
     * 获取微信扩展模块命名空间
     * 微信扩展模块: 放置在@app/modules/wechat/modules下
     * 专用模块微信扩展: **设置**为该模块下的modules/wechat目录 (优先级会高于微信扩展模块)
     * @param $name
     * @return string
     */
    public function getModuleNamespace($name)
    {
        return 'app\\modules\\' . (Yii::$app->hasModule($name) ?  "{$name}\\modules\\wechat" : "wechat\\modules\\{$name}");
    }
}