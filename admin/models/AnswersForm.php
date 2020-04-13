<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 18.07.18
 * Time: 12:16
 */

namespace admin\models;


use common\models\MsSurvey;
use common\models\MsSurveyDate;
use common\models\Question;
use common\models\QuestionAnswer;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

class AnswersForm extends Model
{
    const SCENARIO_TEXT_REQUIRED = 'with-text';
    const SCENARIO_TEXT_NOT_REQUIRED = 'without-text';

    /**
     * @var array $texts
     */
    public $texts;
    public $q_ids;
    public $ids;
    public $visit_date;
    public $employee;

    public function rules()
    {
        return [
            [['texts', 'q_ids'], 'each', 'rule' => ['required'], 'on' => self::SCENARIO_TEXT_REQUIRED],
            [['q_ids'], 'each', 'rule' => ['required'], 'on' => self::SCENARIO_TEXT_NOT_REQUIRED],
            ['q_ids', 'each', 'rule' => ['integer']],
            ['employee', 'string', 'max' => 255],
            ['ids', 'each', 'rule' => ['safe']],
            ['visit_date', 'required'],
            ['employee', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'visit_date' => 'Дата визита',
            'employee' => 'Имя сотрудника, который Вас консультировал'
        ];
    }
}