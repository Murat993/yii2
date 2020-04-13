<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 27.06.18
 * Time: 16:31
 */

namespace common\services;


use common\models\Filial;
use common\models\FilialStructureUnit;
use yii\helpers\ArrayHelper;

class StructureService
{
    const STRUCT_NAME = 0;
    CONST STRUCT_PARENT = 1;

    public function getFilialStructAsMap($client_id)
    {
        $struct = FilialStructureUnit::find()->where(['client_id' => $client_id])->all();
        if ($struct) {
            return ArrayHelper::map($struct, 'id', 'name');
        } else {
            return [];
        }
    }

    /**
     * data to Build Tree for select widget
     * @param null $client_id
     * @param null $selected_id
     * @return array
     */
    public function getWidgetTreeData($client_id = null, $selected_id = null, $allSelectable = false)
    {
        $query = (new \yii\db\Query())->select("id as id_struc, parent_id, name")->from("filial_structure_unit");
        if ($client_id) {
            $query->where(['client_id' => $client_id]);
        }
        $rows = $query->all();
        return $this->buildTree($rows, 0, $selected_id, $allSelectable);
    }

    /**
     * Build Tree for checkbox widget, add filials to tree
     * @param null $client_id
     * @param null $selected_ids
     * @return array
     */
    public function getWidgetTreeDataWithFilials($client_id = null, $selected_ids = null)
    {
        $query = (new \yii\db\Query())->select("id as id_struc, parent_id, name")->from("filial_structure_unit");
        if ($client_id) {
            $query->where(['client_id' => $client_id]);
        }
        $rows = $query->all();
        foreach ($rows as &$element) {
            $element['hideCheckbox'] = true;
            $filials = (new \yii\db\Query())
                ->select("id as id_filial, name as text")
                ->from("filial")
                ->where(['filial_structure_unit_id' => $element['id_struc']])
                ->all();
            foreach ($filials as $filial) {
                if ($selected_ids && in_array($filial['id_filial'], $selected_ids)) {
                    $filial['state'] = [
                        'checked' => true
                    ];
                }
                $element['nodes'][] = $filial;
            }
        }
        return $this->buildTreeWithFilials($rows, 0);
    }

    /**
     * Build Tree for select widget
     * @param array $elements
     * @param int $parentId
     * @param null $selected_id
     * @return array
     */
    private function buildTree(array $elements, $parentId = 0, $selected_id = null, $allSelectable = false)
    {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id_struc'], $selected_id, $allSelectable);
                if ($children) {
                    $element['nodes'] = $children;
                    if (!$allSelectable) {
                        $element['selectable'] = false;
                    }

                } else {
                    $element['selectable'] = true;
                }
                $element['text'] = $element['name'];
                unset($element['name']);
                unset($element['parent_id']);
                if ($element['id_struc'] == $selected_id) {
                    $element['state'] = [
                        'selected' => true,
                        'checked' => true
                    ];
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    //todo перепроверить метод

    /**
     * Build Tree for checkbox widget, add filials to tree
     * @param array $elements
     * @param int $parentId
     * @param null $selected_id
     * @return array
     */
    public function buildTreeWithFilials(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTreeWithFilials($elements, $element['id_struc']);
                if ($children) {
                    $element['nodes'] = $children;
                }
                $element['text'] = $element['name'];
                unset($element['name']);
                unset($element['parent_id']);
                $element['selectable'] = false;
                $element['checkable'] = false;
                $branch[] = $element;
            }
        }
        return $branch;

    }

    public function getFilialsStructAsMap($id)
    {
        $objects = Filial::find()->where(['parent_id' => $id])->with('filialStructureUnit')->all();
        if ($objects) {
            $result = ArrayHelper::map($objects, 'id', 'name', 'filialStructureUnit.name');
            ksort($result);
            return $result;
        } else {
            return [];
        }


    }

    public function import($item, $client_id)
    {
        return $this->getOrCreateStructUnit($item, $client_id);

    }

    private function findByName($name, $client_id)
    {
        return FilialStructureUnit::find()->where(['like', 'name', $name])->andWhere(['client_id' => $client_id])->one();
    }


    private function getOrCreateStructUnit($item, $client_id)
    {
        $struct = $this->findByName($item[self::STRUCT_NAME], $client_id);
        if ($item[self::STRUCT_PARENT]) {
            $foundParent = $this->findByName($item[self::STRUCT_PARENT], $client_id);
            $parent = $foundParent ?: $this->createStructUnit($item[self::STRUCT_PARENT], $client_id);
        } else {
            $parent = null;
        }
        if ($struct) {
            if ($struct->parent->id == $parent->id) {
                \Yii::info("CSV FilialStructureUnit EXISTS: {$struct->id} {$struct->name}");
                return $struct;
            } else {
                $struct->parent_id = $parent->id;
                if ($struct->save()) {
                    \Yii::info("CSV FilialStructureUnit UPDATED PARENT: {$struct->id} {$struct->name} parent_ID = {$parent->id}");
                    return $struct;
                }
            }
        } else {
            $level = $parent ? ($parent->level + 1) : 1;
            return $this->createStructUnit($item[self::STRUCT_NAME], $client_id, $parent ? $parent->id : null, $level);
        }
    }

    public function getOrCreateStructUnitByFilial($item, $client_id)
    {
        $struct = $this->findByName($item, $client_id);
        if ($struct) {
            \Yii::info("CSV FILIAL UPLOAD -->>> FilialStructureUnit EXISTS: {$struct->id} {$struct->name}");
            return $struct;
        } else {
            return $this->createStructUnit($item, $client_id);
        }
    }

    private function createStructUnit($name, $client_id, $parent_id = null, $level = 1)
    {
        $model = new FilialStructureUnit();
        $model->client_id = $client_id;
        $model->name = $name;
        $model->parent_id = $parent_id;
        $model->level = $level;
        if ($model->save()) {
            \Yii::info("CSV FilialStructureUnit UPLOAD: {$model->id} {$model->name}, true");
            return $model;
        } else {
            \Yii::info("CSV FilialStructureUnit UPLOAD: {$model->id} {$model->name}, false");
            return false;
        }
    }


}