<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 07.08.18
 * Time: 11:31
 */

namespace supervisor\models;


use common\models\MsSurvey;
use yii\base\Model;

class MsSurveySearchForm extends Model
{
    public $status = MsSurvey::STATUS_MODERATION;
    public $ms_id;
    public $geo_country;
    public $city_id;
    public $date;

    public function rules()
    {
        return [
            [['status', 'ms_id', 'city_id', 'geo_country', 'date'], 'safe']
        ];
    }


}