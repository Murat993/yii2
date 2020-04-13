<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 07.08.18
 * Time: 9:32
 */

namespace common\services;


use common\models\MsSurvey;
use common\models\MsSurveyDate;
use common\models\QuestionAnswer;
use common\models\QuestionCheck;
use common\models\Survey;
use Yii;
use yii\helpers\ArrayHelper;

class SurveyService
{
    public function getSurvey($id)
    {
        return Survey::findOne(['id' => $id]);
    }


    public function isMsSurveyValid($ms_survey_id)
    {
        //TODO somehow answers stored twice
        $msSurvey = MsSurvey::findOne($ms_survey_id);
        $answers = QuestionAnswer::find()->where([
//            'ms_id' => $msSurvey->ms_id,
            'ms_survey_id' => $ms_survey_id,
            'survey_id' => $msSurvey->surveyFilial->survey_id
        ])->groupBy('question_id')->all();
        $ids = ArrayHelper::getColumn($answers, 'id');
        $count = QuestionCheck::find()
            ->where(['in', 'answer_id', $ids])
            ->andWhere(['comment' => null])
            ->count();
        if ($count >= count($ids)) {
            return true;
        } else {
            return false;
        }
    }

    public function createEmptyMsSurvey($survey_filial_id, $count)
    {
        $counter = 0;
        while ($counter++ < $count) {
            $entity = new MsSurvey();
            $entity->survey_filial = $survey_filial_id;
            $entity->status = $entity::STATUS_NEW;
            $entity->save();
        }

    }

    public function unlinkNewMsSurveys($survey_filial_id, $count)
    {
        $counter = 0;
        while ($counter++ < $count) {
            $entity = MsSurvey::findOne(['survey_filial' => $survey_filial_id, 'status' => MsSurvey::STATUS_NEW]);
            $entity->delete();
        }
    }


    public function updateMsSurveys($survey_filial_id, $newTaskCount)
    {
        $currentCount = MsSurvey::find()->where(['survey_filial' => $survey_filial_id])->count();
        switch ($currentCount) {
            case $currentCount < $newTaskCount:
                $this->createEmptyMsSurvey($survey_filial_id, ((int)$newTaskCount - (int)$currentCount));
                return true;
                break;
            case $currentCount > $newTaskCount:
                $diff = $currentCount - $newTaskCount;
                $newStatusCount = MsSurvey::find()->where(['survey_filial' => $survey_filial_id, 'status' => MsSurvey::STATUS_NEW])->count();
                switch ($diff) {
                    case ($diff === $newStatusCount || $diff < $newStatusCount):
                        $this->unlinkNewMsSurveys($survey_filial_id, $diff);
                        return true;
                        break;
                    case $diff > $newStatusCount:
                        return false;
                        break;
                }
                break;
            default:
                return true;
                break;
        }
    }

    public function unlinkMsSurvey($ms_survey_id)
    {
        MsSurveyDate::deleteAll('ms_survey_id = :ms_survey_id', ['ms_survey_id' => $ms_survey_id]);
        $ms_survey = MsSurvey::findOne(['id' => $ms_survey_id]);
        if ($ms_survey) {
            $ms_survey->ms_id = null;
            $ms_survey->status = MsSurvey::STATUS_NEW;
            if ($ms_survey->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function countMsSurveysByStatus($query)
    {
        $countCompleted = 0;
        $countAssigned = 0;
        $countNew = 0;
        $countInProcess = 0;
        $countModerationStart = 0;
        $countModeration = 0;
        foreach ($query->all() as $item) {
            switch ($item->status) {
                case MsSurvey::STATUS_COMPLETED:
                    $countCompleted++;
                    break;
                case MsSurvey::STATUS_MODERATION_START:
                    $countModerationStart++;
                    break;
                case MsSurvey::STATUS_NEW:
                    $countNew++;
                    break;
                case MsSurvey::STATUS_IN_PROCESS:
                    $countInProcess++;
                    break;
                case MsSurvey::STATUS_MS_ASSIGNED:
                    $countAssigned++;
                    break;
                case MsSurvey::STATUS_MODERATION:
                    $countModeration++;
                    break;
                default:
                    break;
            }
        }
        $result['completed'] = $countCompleted;
        $result['assigned'] = $countAssigned;
        $result['new'] = $countNew;
        $result['in_process'] = $countInProcess;
        $result['moderation'] = $countModeration;
        $result['moderation_start'] = $countModerationStart;
        return $result;
    }

    public function getDropdownlist($client_id)
    {
        $lang = Yii::$app->language;
        $res = [];
        $surveys = Survey::find()
            ->with('surveyLang')
            ->where(['client_id' => $client_id])->all();

        foreach ($surveys as $index => $survey) {
            if (!empty($survey->surveyLang) && is_array($survey->surveyLang)) {
                foreach ($survey->surveyLang as $item) {
                    if ($item['lang_id'] == substr($lang, 0, 2)) {
                        $name = $item['name'];
                    } else {
                        $name = Yii::t('app', 'Нет перевода');
                    }
                }
            } else {
                $name = Yii::t('app', 'Нет перевода');
            }
            $res[$survey->id] = $name;
        }
        return $res;
    }


}