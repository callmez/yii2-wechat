<?php

namespace callmez\wechat\modules\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\TagDependency;
use callmez\wechat\components\BaseModule;
use callmez\wechat\models\Module as ModuleModel;

/**
 * 微信模块后台管理子模块
 * @package callmez\wechat\modules\admin
 */
class Module extends BaseModule
{
    /**
     * 后台菜单按钮缓存
     */
    const CACHE_ADMIN_MENUS_KEY = 'wechat_admin_menus_cache';
    /**
     * 模块控制器命名空间
     * @var string
     */
    public $controllerNamespace = 'callmez\wechat\modules\admin\controllers';
    /**
     * 模块名称
     * @var string
     */
    public $name = '微信后台';
    /**
     * 后台模块的基本视图
     * 该视图只是一个子视图, 做为站点后台视图和该模块视图的中间视图
     * @var string
     */
    public $layout = 'main';
    /**
     * 站点后台视图
     * @var string
     */
    public $siteAdminLayout = '@app/views/layouts/main.php';
    /**
     * 菜单类, 必须继承callmez\wechat\models\Menu
     * @var string
     */
    public $menuModelClass = 'callmez\wechat\models\Menu';

    private $_categoryMenus;

    /*
     * 获取后台分类菜单
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
     * 初始默认菜单
     * @var array
     */
    public $defaultCategoryMenus = [
        'system' => [
            'label' => '系统管理',
            'items' => [
                [
                    'label' => '公众号管理',
                    'url' => ['/wechat/admin/wechat/index'],
                ],
            ]
        ],
        'basic' => [
            'label' => '基本功能',
            'items' => [
                [
                    'label' => '回复管理',
                    'url' => ['/wechat/admin/reply/index'],
                ]
            ]
        ],
        'advanced' => [
            'label' => '高级功能',
            'items' => [
                [
                    'label' => '自定义菜单管理',
                    'url' => ['/wechat/admin/menu/index'],
                ],
                [
                    'label' => '二维码管理(待开发)',
                    'url' => ['/wechat/admin/qrcode/index'],
                ],
                [
                    'label' => '卡卷功能(待开发)',
                    'url' => ['/wechat/admin/card/index'],
                ],
                [
                    'label' => '多客服接入(待开发)',
                    'url' => ['/wechat/admin/message/customer'],
                ]
            ]
        ],
        'fans' => [
            'label' => '粉丝营销',
            'items' => [
                [
                    'label' => '粉丝管理',
                    'url' => ['/wechat/admin/fans/index'],
                ],
                [
                    'label' => '粉丝分组(待开发)',
                    'url' => ['/wechat/admin/fans/group'],
                ]
            ]
        ],
        'message' => [
            'label' => '通知中心',
            'items' => [
                [
                    'label' => '通信记录',
                    'url' => ['/wechat/admin/message/index'],
                ],
                [
                    'label' => '客服消息 (待开发)',
                    'url' => ['/wechat/admin/message/cutom'],
                ],
                [
                    'label' => '微信群发 (待开发)',
                    'url' => ['/wechat/admin/message/wechat'],
                ]
            ]
        ],
        'business' => [
            'label' => '主要业务',
            'items' => []
        ],
        'activity' => [
            'label' => '营销活动',
            'items' => []
        ],
        'activity' => [
            'label' => '客户关系',
            'items' => []
        ],
        'service' => [
            'label' => '常用服务',
            'items' => []
        ],
        'module' => [
            'label' => '扩展模块',
            'items' => [
                [
                    'label' => '模块管理',
                    'url' => ['/wechat/admin/module/index'],
                ]
            ]
        ],
        'test' => [
            'label' => '功能测试',
            'items' => [
                [
                    'label' => '微信模拟器',
                    'url' => ['/wechat/admin/simulator/index'],
                ]
            ]
        ],
        'other' => [
            'label' => '其他',
            'items' => []
        ],
    ];

    /**
     * 菜单生成
     * @return array
     */
    protected function categoryMenus()
    {
        $cache = Yii::$app->cache;
        if (($menus = $cache->get(self::CACHE_ADMIN_MENUS_KEY)) === false) {
            $menus = $this->defaultCategoryMenus;

            $categories = $this->getCategories();
            $class = $this->module->moduleModelClass;
            foreach ($class::findAll(['admin' => 1]) as $model) { // 安装的扩展模块(开启开启后台功能)
                $key = isset($categories[$model->category]) ? $model->category : $categories['module'];
                $menus[$key]['items'][] = [
                    'label' => $model->name,
                    'url' => Yii::$app->getModule('wechat/' . $model->id)->getAdminHomeUrl()
                ];
            }

            $cache->set(self::CACHE_ADMIN_MENUS_KEY, $menus, null, new TagDependency([
                'tags' => [ModuleModel::CACHE_DATA_DEPENDENCY_TAG]
            ]));
        }
        return $menus;
    }

    private $_categories;

    /**
     * 根据默认菜单获取分类
     * @return array
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            foreach ($this->defaultCategoryMenus as $key => $menus) {
                $this->_categories[$key] = isset($menus['label']) ? $menus['label'] : $key;
            }
        }

        return $this->_categories;
    }
}
