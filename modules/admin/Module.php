<?php

namespace callmez\wechat\modules\admin;

use Yii;

/**
 * 微信模块后台管理子模块
 * @package callmez\wechat\modules\admin
 */
class Module extends \yii\base\Module
{
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
    public $layout = 'menu';
    /**
     * 站点后台视图
     * @var string
     */
    public $siteAdminLayout = '@app/views/layouts/main.php';

    /**
     * 默认的后台管理菜单
     * @var array
     */
    public $defaultMenus = [
        'system' => [
            'label' => '系统管理',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-cog"></span> 公众号管理',
                    'url' => ['/wechat/admin/wechat/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ],
            ]
        ],
        'basic' => [
            'label' => '基本功能',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-envelope"></span> 回复规则',
                    'url' => ['/wechat/admin/rule/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ]
            ]
        ],
        'advanced' => [
            'label' => '高级功能',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-list"></span> 自定义菜单管理',
                    'url' => ['/wechat/admin/menu/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ]
            ]
        ],
        'fans' => [
            'label' => '粉丝营销',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-user"></span> 粉丝管理',
                    'url' => ['/wechat/admin/fans/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-user"></span> 粉丝分组(待开发)',
                    'url' => ['/wechat/admin/fans/group'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ]
            ]
        ],
        'message' => [
            'label' => '通知中心',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-floppy-disk"></span> 通信记录',
                    'url' => ['/wechat/admin/message/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-user"></span> 客服消息',
                    'url' => ['/wechat/admin//index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-user"></span> 微信群发',
                    'url' => ['/wechat/admin/fans/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ]
            ]
        ],
        'test' => [
            'label' => '功能测试',
            'items' => [
                [
                    'label' => '<span class="glyphicon glyphicon-send"></span> 微信模拟器',
                    'url' => ['/wechat/admin/simulator/index'],
                    'options' => [
                        'class' => 'list-group-item'
                    ]
                ]
            ]
        ],
    ];

    /**
     * @var array
     */
    private $_menus;
    /**
     * 设置微信后台的管理菜单
     * @param array $menus
     */
    public function setMenus(array $menus)
    {
        $this->_menus = $menus;
    }

    /**
     * 获取微信后台的管理菜单
     * @return mixed|null
     */
    public function getMenus()
    {
        if ($this->_menus === null) {
            $this->setMenus($this->defaultMenus);
        }
        return $this->_menus;
    }

}
