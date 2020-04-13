<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 04.09.2018
 * Time: 9:50
 */

namespace common\services;


use common\models\Scenario;

class ScenarioService
{
    public function getScenario($id)
    {
        return Scenario::findOne($id);
    }

}
