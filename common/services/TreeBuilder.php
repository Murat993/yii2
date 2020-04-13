<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 28.11.2018
 * Time: 15:57
 */

namespace common\services;


use yii\helpers\ArrayHelper;

class TreeBuilder
{

    public function getTree($query)
    {
        $data = \Yii::$app->db->createCommand($query)->queryAll();
        $parents = [];
        $childs = [];
        $tree = [];
        foreach ($data as $id => &$node) {
            if (empty($node['parent_id'])) {
                $parents[] = &$node;
            } else {
                $childs[] = &$node;
            }
        }
        foreach ($parents as $key => $parent) {
            $tree[$key] = $parent;
            $tree[$key]['childs'] = $this->childTree($childs, $parent['id'], $parent['tableName']);
        }
        return $tree;
    }

    protected function childTree($childs, $parent_id, $type)
    {
        $childTree = [];
        foreach ($childs as $k => $child) {
            if ($child['parent_id'] == $parent_id && $child['tableName'] !== $type) {
                $childTree[$k] = $child;
                ArrayHelper::remove($childs, $k);
                $childTree[$k]['childs'] = $this->childTree($childs, $child['id'], $child['tableName']);
            } elseif ($child['parent_id'] === $parent_id && $child['tableName'] == 'article') {
                $childTree[$k] = $child;
                ArrayHelper::remove($childs, $k);
                $childTree[$k]['childs'] = $this->childTree($childs, $child['id'], $child['tableName']);
            }
        }
        return $childTree;
    }
}