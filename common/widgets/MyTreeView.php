<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 03.10.18
 * Time: 15:53
 */

namespace common\widgets;


use yii\base\Widget;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;

class MyTreeView extends Widget
{
    public $data;
    public $tree;
    public $menuHtml;
    public $msId;
    public $query;
    private $colorMap;

    public function run()
    {
        $this->colorMap = Yii::$app->userService->getClientColorMap();
        $this->tree = Yii::$app->treeBuilder->getTree($this->query);
        $this->menuHtml = $this->getMenuHtml($this->tree);
        return $this->menuHtml;
    }

    protected function getMenuHtml($tree)
    {
        $str = '';
        foreach ($tree as $category) {
            $str .= $this->catToTemplate($category, $this->colorMap);
        }
        return $str;
    }

    protected function catToTemplate($category, $colorMap)
    {
        ob_start();
        include __DIR__ . '/tree/_tree.php';
        return ob_get_clean();
    }
}