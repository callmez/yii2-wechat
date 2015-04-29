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
                    'label' => '<span class="fa fa-cog"></span> 公众号管理',
                    'url' => ['/wechat/admin/wechat/index'],
                ],
            ]
        ],
        'basic' => [
            'label' => '基本功能',
            'items' => [
                [
                    'label' => '<span class="fa fa-reply"></span> 回复规则',
                    'url' => ['/wechat/admin/rule/index'],
                ],
                [
                    'label' => '<span class="fa fa-font"></span> 文本回复(待开发)',
                    'url' => ['/wechat/admin/reply/text'],
                ],
                [
                    'label' => '<span class="fa fa-newspaper-o"></span> 图文回复(待开发)',
                    'url' => ['/wechat/admin/reply/news'],
                ],
                [
                    'label' => '<span class="fa fa-music"></span> 音乐回复(待开发)',
                    'url' => ['/wechat/admin/reply/music'],
                ],
                [
                    'label' => '<span class="fa fa-image"></span> 图片回复(待开发)',
                    'url' => ['/wechat/admin/reply/photo'],
                ],
                [
                    'label' => '<span class="fa fa-volume-up"></span> 语音回复(待开发)',
                    'url' => ['/wechat/admin/reply/voice'],
                ],
                [
                    'label' => '<span class="fa fa-video-camera"></span> 视频回复(待开发)',
                    'url' => ['/wechat/admin/reply/vedio'],
                ],
                [
                    'label' => '<span class="fa fa-cloud"></span> 远程回复(待开发)',
                    'url' => ['/wechat/admin/reply/remote'],
                ],
                [
                    'label' => '<span class="fa fa-crop"></span> 素材管理(待开发)',
                    'url' => ['/wechat/admin/media/index'],
                ],
            ]
        ],
        'advanced' => [
            'label' => '高级功能',
            'items' => [
                [
                    'label' => '<span class="fa fa-bars"></span> 自定义菜单管理',
                    'url' => ['/wechat/admin/menu/index'],
                ],
                [
                    'label' => '<span class="fa fa-qrcode"></span> 二维码管理(待开发)',
                    'url' => ['/wechat/admin/qrcode/index'],
                ],
                [
                    'label' => '<span class="fa fa-mastercard"></span> 卡卷功能(待开发)',
                    'url' => ['/wechat/admin/card/index'],
                ],
                [
                    'label' => '<span class="fa fa-user-plus"></span> 多客服接入(待开发)',
                    'url' => ['/wechat/admin/message/customer'],
                ]
            ]
        ],
        'fans' => [
            'label' => '粉丝营销',
            'items' => [
                [
                    'label' => '<span class="fa fa-user"></span> 粉丝管理',
                    'url' => ['/wechat/admin/fans/index'],
                ],
                [
                    'label' => '<span class="fa fa-group"></span> 粉丝分组(待开发)',
                    'url' => ['/wechat/admin/fans/group'],
                ]
            ]
        ],
        'message' => [
            'label' => '通知中心',
            'items' => [
                [
                    'label' => '<span class="fa fa-history"></span> 通信记录',
                    'url' => ['/wechat/admin/message/index'],
                ],
                [
                    'label' => '<span class="fa fa-comment-o"></span> 客服消息 (待开发)',
                    'url' => ['/wechat/admin/message/cutom'],
                ],
                [
                    'label' => '<span class="fa fa-wechat"></span> 微信群发 (待开发)',
                    'url' => ['/wechat/admin/message/wechat'],
                ]
            ]
        ],
        'module' => [
            'label' => '扩展模块',
            'items' => [
                [
                    'label' => '<span class="fa fa-connectdevelop"></span> 模块管理(待开发)',
                    'url' => ['/wechat/admin/module/index'],
                ]
            ]
        ],
        'test' => [
            'label' => '功能测试',
            'items' => [
                [
                    'label' => '<span class="fa fa-bug"></span> 微信模拟器',
                    'url' => ['/wechat/admin/simulator/index'],
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
