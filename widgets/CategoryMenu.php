<?php
namespace callmez\wechat\widgets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\widgets\Menu;
use yii\helpers\ArrayHelper;
use yii\bootstrap\BootstrapPluginAsset;

/**
 * 微信分类菜单
 * @package callmez\wechat\widgets
 */
class CategoryMenu extends Menu
{
    /**
     * @inheritdoc
     */
    public $options = [
        'class' => 'list-unstyled panel-group',
    ];
    /**
     * @inheritdoc
     */
    public $labelTemplate = '<div class="panel-heading" {accordionData}><h4 class="panel-title cursor-pointer"><span class="fa fa-angle-down pull-right"></span>{label}</h4></div>';
    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class='list-group'>\n{items}\n</ul>\n";
    /**
     * @inheritdoc
     */
    public $itemOptions = [
        'class' => 'panel panel-default'
    ];
    /**
     * @inheritdoc
     */
    public $activateParents = true;
    /**
     * 子项默认设置
     * @var array
     */
    public $subItemOptions = [
        'class' => 'list-group-item'
    ];
    /**
     * 开启折叠
     * @var string
     */
    public $accordion = true;

    protected $accordionId;

    /**
     * Renders the menu.
     */
    public function run()
    {
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        $items = $this->normalizeItems($this->items, $hasActiveChild);
        if (!empty($items)) {
            if ($this->accordion) {
                $this->accordionId = $this->options['id'] = ArrayHelper::getValue($this->options, 'id', $this->getId());
                $this->registerPlugin('collapse');
            }
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'ul');

            if ($tag !== false) {
                echo Html::tag($tag, $this->renderItems($items), $options);
            } else {
                echo $this->renderItems($items);
            }
        }
    }

    /**
     * @inheritdoc
     * @param boolean $isSubItem 增加subItemOptions的判断
     */
    protected function renderItems($items, $isSubItem = false)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, $isSubItem ? $this->subItemOptions : [], ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }

            $menu = $this->renderItem($item);
            if ($this->accordion) {
                $menu = strtr($menu, [
                    '{accordionData}' => 'data-toggle="collapse" data-parent="#' . $this->accordionId . '" data-target="#collapse' . $i . '"'
                ]);
            }
            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $submenu = strtr($submenuTemplate, [
                    '{items}' => $this->renderItems($item['items'], true),
                ]);
                if ($this->accordion) {
                    $submenu = Html::tag('div', $submenu, [
                        'id' => 'collapse' . $i,
                        'class' => 'panel-collapse collapse' . ($item['active'] ? 'in' : '')
                    ]);
                }
                $menu .= $submenu;
            }
            if ($tag === false) {
                $lines[] = $menu;
            } else {
                $lines[] = Html::tag($tag, $menu, $options);
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Registers a specific Bootstrap plugin and the related events
     * @param string $name the name of the Bootstrap plugin
     */
    protected function registerPlugin($name)
    {
        $view = $this->getView();

        BootstrapPluginAsset::register($view);

        $id = $this->options['id'];

        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#$id').$name($options);";
        $view->registerJs($js);
    }
}
