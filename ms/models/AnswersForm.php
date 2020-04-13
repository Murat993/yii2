<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 18.07.18
 * Time: 12:16
 */

namespace ms\models;


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

    public function save($ms_survey_id)
    {
        if ($this->visit_date) {
            $date = new MsSurveyDate();
            $date->ms_survey_id = $ms_survey_id;
            $date->date = $this->visit_date;
            $date->employee_name = $this->employee;
            $date->type = MsSurveyDate::DATE_TYPE_VISITED;
            if ($date->save()) {
                return true;
            } else {
                return false;
            }
        }

    }

    public function checkAnswers($ms_survey_id, $survey_id)
    {
        $answers = QuestionAnswer::findAll(['ms_survey_id' => $ms_survey_id]);
        $questions = Question::find()
            ->innerJoin('article', 'question.article_id = article.id')
            ->innerJoin('questionary', 'article.questionary = questionary.id')
            ->innerJoin('survey', 'questionary.id = survey.questionary_id')
            ->innerJoin('survey_filial', 'survey.id = survey_filial.survey_id')
            ->innerJoin('ms_survey', "survey_filial.id = ms_survey.survey_filial")
            ->where(['ms_survey.id' => $ms_survey_id])->select('question.id')
            ->all();
        if ($answers) {
            if (count($answers) === count($questions)) {
                return true;
            } else {
                $answerQIds = ArrayHelper::map($answers, 'id', 'question_id');
                foreach ($questions as $question) {
                    $resl = ArrayHelper::isIn($question->id, $answerQIds);
                    if (!$resl) {
                        $this->generateEmptyAnswer($question->id, $survey_id, $ms_survey_id);
                    }
                }
            }
        } else {
            foreach ($questions as $question) {
                $this->generateEmptyAnswer($question->id, $survey_id, $ms_survey_id);
            }
        }
        return true;
    }

    private function generateEmptyAnswer($question_id, $survey_id, $ms_survey_id)
    {
        $questionAnswer = new QuestionAnswer;
        $questionAnswer->question_id = $question_id;
        $questionAnswer->text = '';
        $questionAnswer->ms_id = Yii::$app->user->getId();
        $questionAnswer->survey_id = $survey_id;
        $questionAnswer->ms_survey_id = $ms_survey_id;
        return $questionAnswer->save();
    }


    public function calcScenario()
    {
        $settingsLength = Yii::$app->systemSettingsService->getSystemSettings()->answerLength;
        if ((int)$settingsLength) {
            if ($settingsLength == 0) {
                $this->setScenario(self::SCENARIO_TEXT_NOT_REQUIRED);
            } else {
                $this->setScenario(self::SCENARIO_TEXT_REQUIRED);
            }
        } else {
            $this->setScenario(self::SCENARIO_TEXT_NOT_REQUIRED);
        }
    }
}