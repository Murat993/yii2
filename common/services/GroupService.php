<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 27.06.18
 * Time: 9:16
 */

namespace common\services;


use common\models\GroupTemplate;
use yii\helpers\ArrayHelper;

class GroupService
{
    public function getAllGroups()
    {
        return GroupTemplate::find()->all();
    }

    public function getGroupsAsMap()
    {
        $groups = $this->getAllGroups();
        if ($groups){
            return ArrayHelper::map($groups, 'id', 'name');
        }else{
            return [];
        }
    }
}