<?php

namespace callmez\wechat\modules\admin;

use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\TagDependency;
use callmez\wechat\models\Menu;

/**
 * 微信模块后台管理子模块
 * @package callmez\wechat\modules\admin
 */
class Module extends \yii\base\Module
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
    public $menuClass = 'callmez\wechat\models\Menu';

    private $_menus;

    /*
     * 获取后台菜单
     */
    public function getMenus()
    {
        if ($this->_menus === null) {
            $this->setMenus($this->menus());
        }
        return $this->_menus;
    }

    /**
     * 设置后台菜单
     * @param $menus
     * @return mixed
     */
    public function setMenus($menus)
    {
        return $this->_menus = $menus;
    }

    /**
     * 初始默认菜单
     * @var array
     */
    public $defaultMenus = [
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
//                [
//                    'label' => '消息回复',
//                    'url' => ['/wechat/admin/reply/index'],
//                ],
                [
                    'label' => '文本回复(待开发)',
                    'url' => ['/wechat/admin/reply/text'],
                ],
                [
                    'label' => '图文回复(待开发)',
                    'url' => ['/wechat/admin/reply/news'],
                ],
                [
                    'label' => '音乐回复(待开发)',
                    'url' => ['/wechat/admin/reply/music'],
                ],
                [
                    'label' => '图片回复(待开发)',
                    'url' => ['/wechat/admin/reply/photo'],
                ],
                [
                    'label' => '语音回复(待开发)',
                    'url' => ['/wechat/admin/reply/voice'],
                ],
                [
                    'label' => '视频回复(待开发)',
                    'url' => ['/wechat/admin/reply/vedio'],
                ],
                [
                    'label' => '远程回复(待开发)',
                    'url' => ['/wechat/admin/reply/remote'],
                ],
                [
                    'label' => '素材管理(待开发)',
                    'url' => ['/wechat/admin/media/index'],
                ],
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
    protected function menus()
    {
        $cache = Yii::$app->cache;
        if (($menus = $cache->get(self::CACHE_ADMIN_MENUS_KEY)) === false) {
            $menus = $this->defaultMenus;

            $categories = $this->getCategories();

            $class = $this->module->moduleClass;
            foreach ($class::findAll(['admin' => 1]) as $model) { // 安装的扩展模块(开启开启后台功能)
                $key = isset($categories[$model->category]) ? $model->category : $categories['module'];
                $menus[$key]['items'][] = [
                    'label' => $model->name,
                    'url' => ['/wechat/' . $model->id . '/admin/index'], // 取AdminController::actionIndex()为模块默认后台首页
                ];
            }

            $class = $this->menuClass;
            foreach ($class::find()->andWhere(['type' => Menu::TYPE_ADMIN])->all() as $model) { // 注册的后台菜单
                if (!isset($categories[$model->category])) {
                    continue;
                }
                $menus[$model->category]['items'][] = [
                    'label' => $model->title,
                    'url' => $model->router
                ];
            }

            $cache->set(self::CACHE_ADMIN_MENUS_KEY, $menus, null, new TagDependency([
                'tags' => [$class::CACHE_DATA_DEPENDENCY_TAG, Menu::CACHE_DATA_DEPENDENCY_TAG]
            ]));
        }
        return $menus;
    }

    public function getModuleMenus()
    {
        return [];
    }

    private $_categories;

    /**
     * 根据默认菜单获取分类
     * @return array
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            foreach ($this->defaultMenus as $key => $menus) {
                $this->_categories[$key] = isset($menus['label']) ? $menus['label'] : $key;
            }
        }

        return $this->_categories;
    }
}
