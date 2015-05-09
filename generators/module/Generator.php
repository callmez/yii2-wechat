<?php
namespace callmez\wechat\generators\module;

use Yii;
use yii\helpers\Html;
use yii\gii\CodeFile;
use yii\helpers\StringHelper;
use Symfony\Component\Yaml\Yaml;

/**
 * 微信模块生成器
 */
class Generator extends \yii\gii\Generator
{
    public $moduleID;
    public $moduleName;
    public $moduleClass;
    public $version = '1.0.0';
    public $migration;
    public $admin = true;
    public $replyRule = true;
    public $author;
    public $site;
    public $category;

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
        return '该生成器将帮助您生成一个基础的微信扩展模块.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID', 'moduleClass', 'moduleName'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'moduleName', 'moduleClass', 'version'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => '{attribute}只能包含字母,数字和-_符号.'],
            [['moduleClass'], 'validateModuleClass'],
            [['version'], 'match', 'pattern' => '/^\d+[.\d]+\d+$/', 'message' => '{attribute}只能包含数字和.符号并符合版本命名规则, 例如<code>1.0.0</code>'],
            [['migration', 'admin', 'replyRule'], 'boolean'],

            [['author'], 'string', 'max' => 50],
            [['site'], 'string', 'max' => 255],
            [['category'], 'in', 'range' => array_keys($this->getCategories()), 'skipOnEmpty' => false],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => '模块ID',
            'moduleName' => '模块名称',
            'moduleClass' => '模块类名',
            'version' => '模块版本',
            'migration' => '是否需要迁移数据',
            'admin' => '是否需要后台管理界面',
            'replyRule' => '是否启用回复规则功能',
            'author' => '作者',
            'site' => '模块详情地址',
            'category' => '模块所属分类'
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return [
            'moduleID' => '模块ID必须是唯一的. 比如:<code>example</code>',
            'moduleName' => '模块名称是模块的一个简称',
            'moduleClass' => '根据模块名生成模块类, 不能修改. 比如:<code>app\modules\example\Module</code>',
            'migration' => '模块安装, 升级和卸载需要创建的表或生成的数据. <br>如果勾选, 会在模块目录下生成Migration迁移类文件,您可以在里面书写数据迁移代码. <br>比如<code>app\modules\example\Migration</code>',
            'admin' => '模式是否需要后台管理界面. 如果勾选将生成默认后台管理处理类',
            'replyRule' => '是否启用回复规则路由功能, 如果勾选将会在后台管理中显示模块的回复规则菜单. <br><code>该功能需勾选后台管理界面</code>',
            'author' => '请留下您的大名吧!!!!',
            'site' => '模块的详细介绍地址, 需带上<code>http(s):://</code>',
            'category' => '模块所属分类, 该分类将会确定该模块所在的菜单位置'
        ];
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        $output = <<<EOD
<p>恭喜! 模块已经生成成功.</p>
<p>如果需要使用该模块, 您需要到微信后台中安装此模块并按照您的需求开发功能. </p>
EOD;
        return $output;
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return [
            'module.php',
            'processController.php',
            'adminController.php',
            'adminView.php',
            'wechatMigration.php',
            'wechat.yml'
        ];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/ProcessController.php',
            $this->render("processController.php")
        );
        if ($this->migration) {
            $files[] = new CodeFile(
                $modulePath . '/migrations/WechatMigration.php',
                $this->render("wechatMigration.php")
            );
            $files[] = new CodeFile(
                $modulePath . '/views/admin/index.php',
                $this->render("adminView.php")
            );
        }
        if ($this->admin) {
            $files[] = new CodeFile(
                $modulePath . '/controllers/AdminController.php',
                $this->render("adminController.php")
            );
        }
        $files[] = new CodeFile(
            $modulePath . '/wechat.yml',
            $this->render("wechat.yml", [
                'content' => Yaml::dump([
                    'id' => $this->moduleID,
                    'name' => $this->moduleName,
                    'version' => $this->version,
                    'author' => $this->author,
                    'site' => $this->site,
                    'category' => $this->category,
                    'admin' => (bool) $this->admin,
                    'migration' => (bool) $this->migration,
                    'replyRule' => (bool) $this->replyRule
                ])
            ])
        );

        return $files;
    }

    /**
     * 验证模块类名
     */
    public function validateModuleClass()
    {
        $class = 'app\modules\wechat\modules\\' . $this->moduleID . '\Module';
        if ($this->moduleClass != $class) {
            $this->addError('moduleClass', '模块类名必须为 ' . $class);
        }
    }

    /**
     * @return boolean the directory that contains the module class
     */
    public function getModulePath()
    {
        return Yii::getAlias('@' . str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\controllers';
    }

    /**
     * 获取菜单可选
     * @return mixed
     */
    public function getCategories()
    {
        return Yii::$app->getModule('wechat/admin')->getCategories();
    }
}
