<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 05.07.18
 * Time: 11:55
 */

namespace admin\models;


use yii\base\Model;
use Yii;
use yii\behaviors\AttributeTypecastBehavior;

class AddFilialForm extends Model
{
    public $country_id;
    public $city_id;
    public $filial_id;
    public $scenario_id;
    public $supervisor_id;
    public $task_count;
    public $comment;
    public $instruction;

    public function rules()
    {
        return [
            [['filial_id', 'scenario_id', 'supervisor_id', 'task_count'], 'required'],
            [['country_id', 'city_id', 'filial_id', 'supervisor_id', 'task_count'], 'integer'],
            [['comment', 'scenario_id','instruction'], 'string'],
        ];
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class' => AttributeTypecastBehavior::className(),
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'country_id' => Yii::t('app', 'Страна'),
            'city_id' => Yii::t('app', 'Город'),
            'filial_id' => Yii::t('app', 'Объект'),
            'scenario_id' => Yii::t('app', 'Сценарий'),
            'supervisor_id' => Yii::t('app', 'Супервайзер'),
            'task_count' => Yii::t('app', 'Количество заданий'),
            'comment' => Yii::t('app', 'Комментарий'),
            'instruction' => Yii::t('app', 'Инструкция'),
        ];
    }


}