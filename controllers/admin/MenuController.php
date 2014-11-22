<?php
namespace callmez\wechat\controllers\admin;

use Yii;
use callmez\wechat\components\AdminController;

class MenuController extends AdminController
{
    public function actionIndex()
    {
        $wechat = $this->getMainWechat();
        $menus = json_decode('[{"name":"\u6d3b\u52a8\u6f14\u793a","sub_button":[{"type":"click","name":"\u66f4\u591a\u6d3b\u52a8","key":"\u66f4\u591a\u6d3b\u52a8","sub_button":[]},{"type":"click","name":"\u522e\u522e\u5361","key":"\u6f14\u793a\u522e\u522e\u5361","sub_button":[]},{"type":"click","name":"\u780d\u4ef7\u6d3b\u52a8","key":"\u6f14\u793a\u780d\u4ef7","sub_button":[]},{"type":"click","name":"\u62a2\u8d2d\u6d3b\u52a8","key":"\u6f14\u793a\u62a2\u8d2d\u6d3b\u52a8","sub_button":[]},{"type":"click","name":"\u6211\u7684\u5956\u54c1","key":"\u6211\u7684\u5956\u54c1","sub_button":[]}]},{"name":"\u9884\u5b9a\u6f14\u793a","sub_button":[{"type":"click","name":"\u672a\u4f7f\u7528\u8ba2\u5355","key":"et0799_oauth\/\u672a\u4f7f\u7528\u8ba2\u5355","sub_button":[]},{"type":"click","name":"\u5f85\u652f\u4ed8\u8ba2\u5355","key":"et0799_oauth\/\u5f85\u652f\u4ed8\u8ba2\u5355","sub_button":[]},{"type":"scancode_waitmsg","name":"\u626b\u4e00\u626b\u9a8c\u7968","key":"ddyz","sub_button":[]},{"type":"click","name":"\u95e8\u7968\u9884\u5b9a","key":"et0799_park\/id=1019,1023,1037,1001,1027,1036,1078,1017,1020","sub_button":[]},{"type":"location_select","name":"\u5468\u8fb9\u666f\u533a","key":"zbjq","sub_button":[]}]},{"name":"\u8054\u7cfb\u5408\u4f5c","sub_button":[{"type":"click","name":"\u5173\u4e8e\u6211\u4eec","key":"\u5173\u4e8e\u6211\u4eec","sub_button":[]},{"type":"click","name":"\u4eba\u5de5\u5ba2\u670d","key":"\u8fdb\u5165\u591a\u5ba2\u670d","sub_button":[]},{"type":"click","name":"\u7535\u5b50\u95e8\u7968\u7cfb\u7edf","key":"\u7535\u5b50\u95e8\u7968\u7cfb\u7edf","sub_button":[]},{"type":"click","name":"\u4ea7\u54c1\u5206\u9500\u5408\u4f5c","key":"\u4ea7\u54c1\u5206\u9500\u5408\u4f5c","sub_button":[]},{"type":"click","name":"\u666f\u533a\u8425\u9500\u5408\u4f5c","key":"\u666f\u533a\u8425\u9500\u5408\u4f5c","sub_button":[]}]}]', true);
//        $menus = $wechat->getMenuList();
        if (!$menus) {
            return $this->message($wechat->getLastErrorInfo('获取菜单列表失败!'));
        }
        return $this->render('index', [
            'wechat' => $wechat,
            'menus' => $menus
        ]);
    }
}