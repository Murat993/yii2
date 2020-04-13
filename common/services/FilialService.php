<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 28.06.18
 * Time: 12:54
 */

namespace common\services;


use admin\models\AddFilialForm;
use common\models\Filial;
use common\models\FilialStructureUnit;
use common\models\Position;
use common\models\Scenario;
use common\models\SurveyFilial;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;

class FilialService
{
    const FILIAL_NAME = 0;
    const PHONE = 1;
    const ADDRESS = 2;
    const EMAIL = 3;
    const CHIEF = 4;
    const COUNTRY = 5;
    const CITY = 6;
    const STRUCT = 7;

    public function getFilialsByClient($client_id, $city_id = null)
    {
        $query = Filial::find()->where(['parent_id' => $client_id]);
        if ($city_id) {
            $query->andWhere(['city_id' => $city_id]);
        }
        return $query->all();
    }

    public function getClientFilialsAsMap($client_id, $city_id = null)
    {
        $filials = $this->getFilialsByClient($client_id, $city_id);
        if ($filials) {
            return ArrayHelper::map($filials, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getFilialStructure($client_id)
    {
        return FilialStructureUnit::find()->where(['client_id' => $client_id])->all();
    }

    public function getFilialStructureAsMap($client_id)
    {
        $structure = $this->getFilialStructure($client_id);
        if ($structure) {
            return ArrayHelper::map($structure, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getPositionNamesFilial()
    {
        return [null => ''] + ArrayHelper::map(Position::find()->asArray()->all(), 'id', 'name');
    }


    public function createSurveyFilialFromForm($form, $survey_id)
    {
        $entry = new SurveyFilial();
        $entry->survey_id = (int)$survey_id;
        $entry->filial_id = $form->filial_id;
        $entry->task_count = $form->task_count;
        $entry->comment = $form->comment;
        $entry->supervisor_id = $form->supervisor_id;
        preg_match('/(?<=tag:)(.*)/', $form->scenario_id, $tag);
        if ($tag) {
            $survey = Yii::$app->surveyService->getSurvey($survey_id);
            $scenario = new Scenario();
            $scenario->name = $tag[0];
            $scenario->id_client = $survey->client_id;
            if ($scenario->save()) {
                $entry->id_scenario = $scenario->id;
            }
        } else {
            $entry->id_scenario = $form->scenario_id;
        }
        return $entry;
    }

    public function convertSurveyFilialToForm($survey_filial_id)
    {
        $model = SurveyFilial::findOne(['id' => $survey_filial_id]);
        if ($model) {
            $form = new AddFilialForm();
            $form->filial_id = $model->filial_id;
            $form->task_count = $model->task_count;
            $form->comment = $model->comment;
            $form->supervisor_id = $model->supervisor_id;
            $form->scenario_id = $model->id_scenario;
            return $form;
        }
    }

    public function updateSurveyFilialFromForm($form, $survey_filial_id)
    {
        $model = SurveyFilial::findOne(['id' => $survey_filial_id]);
        if ($model) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->comment = $form->comment;
                $model->task_count = $form->task_count;
                $model->id_scenario = $form->scenario_id;
                $model->supervisor_id = $form->supervisor_id;
                $model->instruction = $form->instruction;
                preg_match('/(?<=tag:)(.*)/', $form->scenario_id, $tag);
                if ($tag) {
                    $scenario = new Scenario();
                    $scenario->name = $tag[0];
                    $scenario->id_client = $model->survey->client_id;
                    if ($scenario->save()) {
                        $model->id_scenario = $scenario->id;
                    }
                } else {
                    $model->id_scenario = $form->scenario_id;
                }
                if ($model->save()) {
                    if (Yii::$app->surveyService->updateMsSurveys($survey_filial_id, $model->task_count)) {
                        $transaction->commit();
                        return true;
                    } else {
                        return false;
                    }
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    public function collectSurveyFilialKeys($survey_id)
    {
        $ids = [];
        $surveyFilials = SurveyFilial::find()->select('id')->where(['survey_id' => $survey_id])->all();
        if ($surveyFilials) {
            foreach ($surveyFilials as $item) {
                $ids[] = $item->id;
            }
            return $ids;
        }
    }

    public function unlinkFilial($id)
    {
        return SurveyFilial::deleteAll(['id' => $id]);
    }

    public function import($item, $client_id)
    {
        $filial = $this->getFilialByName($item[self::FILIAL_NAME], $client_id, $item[self::CITY]);
        $city = Yii::$app->geoService->getOrCreateCityByName($item[self::COUNTRY], $item[self::CITY]);
        $structUnit = null;
        if ($item[self::STRUCT]) {
            $structUnit = Yii::$app->structService->getOrCreateStructUnitByFilial($item[self::STRUCT], $client_id) ?: null;
        }
        if (!$city) {
            return false;
        }
        if ($filial) {
            $filial->phone = $item[self::PHONE];
            $filial->address = $item[self::ADDRESS];
            $filial->email = $item[self::EMAIL];
            $filial->chief_name = $item[self::CHIEF];
            $filial->city_id = $city->id;
            $filial->filial_structure_unit_id = $structUnit ? $structUnit->id : null;
            $result = $filial->save();
            Yii::info("CSV Filial SAVING: {$filial->id} {$filial->name},{$result}");
            return $result;
        } else {
            $model = new Filial();
            $model->name = $item[self::FILIAL_NAME];
            $model->phone = (string)$item[self::PHONE];
            $model->address = $item[self::ADDRESS];
            $model->email = $item[self::EMAIL];
            $model->chief_name = $item[self::CHIEF];
            $model->city_id = $city->id;
            $model->filial_structure_unit_id = $structUnit ? $structUnit->id : null;
            $model->parent_id = $client_id;
            $result = $model->save();
            Yii::info("CSV Filial SAVING: {$model->id} {$model->name},{$result}");
            return $result;
        }
    }

    public function getFilialByName($name, $client_id, $cityName = null)
    {
        $query = Filial::find()->where(['like', 'name', $name])->andWhere(['parent_id' => $client_id]);
        if ($cityName) {
            $city = Yii::$app->geoService->getCityByName($cityName);
            if ($city) {
                $query->andWhere(['city_id' => $city->id]);
            }
        }
        return $query->one();
    }

    public function getDropdownlist($client_id)
    {
        $res = [];
        $filials = Filial::find()->where(['parent_id' => $client_id])->all();
        foreach ($filials as $filial) {
            $res[$filial->id] = $filial->name;
        }
        return $res;
    }

}