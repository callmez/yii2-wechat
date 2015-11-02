<?php
namespace callmez\wechat;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\caching\TagDependency;
use callmez\wechat\models\Wechat;
use callmez\wechat\helpers\ModuleHelper;
use callmez\wechat\components\BaseModule;
use callmez\wechat\models\Module as ModuleModel;

// 加载默认设置文件
//require_once Yii::getAlias('@callmez/wechat/wechat.php');

class Module extends \yii\base\Module
{
    /**
     * 核心模块存放路径
     */
    const CORE_MODULE_PATH = '@callmez/wechat/modules';
    /**
     * 扩展模块存放路径
     */
    const ADDON_MODULE_PATH = '@app/modules/wechat/modules';
    /**
     * 模块数据缓存
     */
    const CACHE_MODULES_DATA = 'cache_wechat_modules_data';
    /**
     * 模块名称
     * @var string
     */
    public $name = '微信';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'callmez\wechat\controllers';
    /**
     * 消息的基本视图
     * @see callmez\wechat\components\BaseController::message()
     * @var string
     */
    public $messageLayout = '@callmez/wechat/views/common/message';
    /**
     * 站点后台视图
     * 如果想让微信模块和站点的后台视图一致, 则修改此视图为站点的后台视图路径即可
     * @var string
     */
    public $siteAdminLayout = '@app/views/layouts/main.php';
    /**
     * 默认的扩展模块存储Model
     * @var string
     */
    public $moduleModelClass = 'callmez\wechat\models\Module';
    /**
     * 默认微信请求接收路由
     * @var string
     */
    public $apiRoute = 'api';
    /**
     * 移动页面公众号查询参数名
     * @see \callmez\wechat\components\MobileController::getWechat();
     * @var string
     */
    public $wechatUrlParam = 'wid';
    /**
     * 微信后台最高管理员账号ID
     * 在该设置中的账号ID将会拥有所以操作权限(不受任何权限影响) 可设置多个
     * 不设置将会按照Yii::$app->user->authManager权限体系来限制, 请确定Yii::$app->user->authManager组件已设置
     * @var string|array
     */
    public $adminId;
    /**
     * 微信后台服务自定义访问验证规则
     * 你可以通过该设置,来自定义控制后台访问权限
     * 参考: yii\filter\AccessRule的配置
     * @var array
     */
    public $adminAccessRule;

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = array_merge([
            'modules' => $this->modules()
        ], $config);
        parent::__construct($id, $parent, $config);
    }

    /**
     * 获取扩展模块列表
     * @return array|mixed
     */
    public function modules()
    {
        $cache = Yii::$app->cache;
        if (($modules = $cache->get(self::CACHE_MODULES_DATA)) === false) {
            $modules = [];

            $model = $this->moduleModelClass;
            foreach ($model::models() as $id => $model) {
                $class = ModuleHelper::getBaseNamespace($model) . '\Module';
                if (!ModuleHelper::isAddonModule($class)) { // 扩展模块必须继承BaseModule
                    continue;
                }
                $modules[$id] = [
                    'class' => $class,
                    'name' => $model['name'],
                ];
            }

            $cache->set(self::CACHE_MODULES_DATA, $modules, null, new TagDependency([
                'tags' => [ModuleModel::CACHE_MODULES_DATA_DEPENDENCY_TAG]
            ]));
        }
        return $modules;
    }

    /**
     * 获取扩展模块后台首页地址
     * @param $module
     * @return array|bool
     */
    public function getModuleAdminHomeUrl($id)
    {
        return $this->hasModule($id) ? ['/wecaht/' . $id . '/admin' ] : false;
    }

    /**
     * 注: 该分类直接影响分类菜单设置, 如果分类菜单没有指定的分类,将不会显示, 请慎重修改
     * @var array
     */
    private $_categories = [
        'system' => '系统管理',
        'basic' => '基本功能',
        'advanced' => '高级功能',
        'fans' => '粉丝营销',
        'message' => '通知中心',
        'business' => '主要业务',
        'activity' => '营销活动',
        'customer' => '客户关系',
        'service' => '常用服务',
        'module' => '扩展模块',
        'test' => '功能测试',
        'other' => '其他'
    ];
    /**
     * 设置所有的模块分类
     * @return mixed
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * 设置所有的模块分类
     * @param array $categories
     */
    public function setCategories($categories)
    {
        foreach ($categories as $key => $name) {
            $this->_categories[$key] = $name;
        }
    }

    /**
     * 设置模块分类
     * @param $key
     * @param $name
     */
    public function setCategory($key, $name)
    {
        $this->_categories[$key] = $name;
    }

    /**
     * 获取模块分类
     * @param $key
     * @return null
     */
    public function getCategory($key)
    {
        return array_key_exists($key, $this->_categories) ? $this->_categories[$key] : null;
    }

    /**
     * @var array
     */
    private $_categoryMenus;

    /**
     * 获取分类菜单
     * @return array|null
     */
    public function getCategoryMenus()
    {
        if ($this->_categoryMenus === null) {
            $this->setCategoryMenus($this->categoryMenus());
        }
        return $this->_categoryMenus;
    }

    /**
     * 设置后台分类菜单
     * @param $menus
     * @return mixed
     */
    public function setCategoryMenus($menus)
    {
        return $this->_categoryMenus = $menus;
    }

    /**
     * 默认的分类菜单
     * @var array
     */
    public $defaultCateMenus = [
        'system' => [
            ['label' => '公众号列表', 'url' => ['/wechat/wechat/index']]
        ],
        'fans' => [
            ['label' => '粉丝列表', 'url' => ['/wechat/fans/index']]
        ],
        'module' => [
            ['label' => '模块管理', 'url' => ['/wechat/module/index']]
        ],
        'test' => [
            ['label' => '微信模拟器', 'url' => ['/wechat/simulator/index']]
        ],
        'advanced' => [
            ['label' => '自定义菜单', 'url' => ['/wechat/menu/index']],
            ['label' => '素材管理', 'url' => ['/wechat/media/index']],
        ]
    ];

    /**
     * 生成分类菜单
     * @return array
     */
    protected function categoryMenus()
    {
        $menus = [];
        $categories = $this->getCategories();
        foreach ($categories as $key => $label) {
            $menus[$key] = [
                'label' => $label,
                'items' => array_key_exists($key, $this->defaultCateMenus) ? $this->defaultCateMenus[$key] : []
            ];
        }

        $class = $this->moduleModelClass;
        foreach ($class::models() as $model) { // 安装的扩展模块(开启后台功能)
            if (!$model['admin'] || !array_key_exists($model['category'], $categories)) {
                continue;
            }
            $menus[$model['category']]['items'][] = [
                'label' => $model['name'],
                'url' => ModuleHelper::getAdminHomeUrl($model['id'])
            ];
        }

        return $menus;
    }

    /**
     * 模块设计器的名字
     *
     * gii的微信generator配置:
     * ```
     *    'generators' => [
     *     [...]
     *     'wechat' => [
     *          'class' => 'callmez\wechat\generators\wechat\Generators'
     *     ],
     *     [...]
     *     ]
     * ```
     *
     * @var string
     */
    public $giiGeneratorName = 'wechat';
    /**
     * gii模块名称
     * @var string
     */
    public $giiModuleName = 'gii';

    /**
     * 模块设计器是否可用
     * @return bool
     */
    public function canCreateModule()
    {
        $gii = Yii::$app->getModule($this->giiModuleName);
        return $gii !== null && isset($gii->generators[$this->giiGeneratorName]);
    }
}
