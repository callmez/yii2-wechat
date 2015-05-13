<?php
namespace callmez\wechat\modules\basic\migrations;

use yii\db\Schema;
use callmez\wechat\models\Menu;
use callmez\wechat\components\ModuleMigration;

/**
 * 模块数据迁移来.处理模块安装,卸载,升级时的数据操作
 */
class WechatMigration extends ModuleMigration
{
    /**
     * 该函数用于模块在安装时执行模块必须数据操作.例如创建数据表,原始数据
     */
    public function install()
    {
        // 创建后台菜单
        $this->addMenu('文本回复', ['/wechat/basic/reply/text']);
        $this->addMenu('图文回复', ['/wechat/basic/reply/news']);
        $this->addMenu('音乐回复', ['/wechat/basic/reply/music']);
        $this->addMenu('图片回复', ['/wechat/basic/reply/photo']);
        $this->addMenu('语音回复', ['/wechat/basic/reply/voice']);
        $this->addMenu('视频回复', ['/wechat/basic/reply/video']);
        $this->addMenu('远程回复', ['/wechat/basic/reply/remote']);
    }

    /**
     * 添加后台菜单
     * @param $title
     * @param $route
     * @param string $category
     * @param string $type
     * @return bool
     */
    public function addMenu($title, $route, $category = 'basic', $type = Menu::TYPE_ADMIN)
    {
        $menu = new Menu();
        $menu->setAttributes([
            'title' => $title,
            'mid' => $this->module->id,
            'route' => $route,
            'type' => $type,
            'category' => $category
        ]);
        return $menu->save();
    }

    /**
     * 删除所有模块菜单
     * @return int
     */
    public function removeAllMenus()
    {
        $result = Menu::deleteAll(['mid' => $this->module->id]);
        if ($result) {
            Menu::updateCache();
        }
        return $result;
    }
    
    /**
     * 该函数用于模块在卸载时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
    public function uninstall()
    {
        $this->removeAllMenus();
    }

    /**
     * 该函数用于模块在升级时执行模块必须数据操作.例如删除模块数据表,模块原始数据
     */
//    public function ungrade($frommVersion, $toVersion)
//    {
//    }
}
