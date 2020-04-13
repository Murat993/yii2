<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 20.09.2018
 * Time: 14:05
 */

namespace common\services;


use common\models\AnswerOptionLang;
use common\models\ArticleLang;
use common\models\QuestionaryLang;
use common\models\QuestionLang;
use common\models\SurveyLang;
use common\models\TaskLang;

class LangService
{
    const ARTICLE = 'article_id';
    const ANSWER_OPTION = 'answer_option_id';
    const QUESTIONARY = 'questionary_id';
    const QUESTION = 'question_id';
    const TASK = 'task_id';
    const SURVEY = 'survey_id';

    private $_service;


    private function generateNewLangClass($entity)
    {
        switch ($entity) {
            case self::ARTICLE :
                return new ArticleLang();
                break;
            case self::ANSWER_OPTION :
                return new AnswerOptionLang();
                break;
            case self::QUESTION :
                return new QuestionLang();
                break;
            case self::SURVEY :
                return new SurveyLang();
                break;
            case self::QUESTIONARY:
                return new QuestionaryLang();
                break;
            case self::TASK:
                return new TaskLang();
                break;
        }
    }

    /**
     * @param $entity
     * @return AnswerOptionLang|ArticleLang|QuestionaryLang|QuestionLang|SurveyLang|TaskLang
     */
    private function initService($entity)
    {
        return $this->_service = $this->generateNewLangClass($entity);
    }

    /**
     * @param $id
     * @param $entity
     * @return mixed
     */
    private function getTranslations($id, $entity)
    {
        return $this->_service->findAll([$entity => $id]);
    }

    /**
     * @param $id
     * @param $entity
     * @return mixed
     */
    private function destroyTranslations($id, $entity)
    {
        return $this->_service->deleteAll([$entity => $id]);
    }


    public function cloneTranslations($oldId, $newId, $entity)
    {
        $this->initService($entity);
        $translations = $this->getTranslations($oldId, $entity);
        if ($translations) {
            $this->destroyTranslations($newId, $entity);
            foreach ($translations as $translation) {
                $newTranslation = $this->generateNewLangClass($entity);
                $newTranslation->attributes = $translation->attributes;
                $newTranslation->{$entity} = $newId;
                if ($entity === self::QUESTIONARY){
                    $newTranslation->name =  $translation->name . ' (копия)';
                }
                $newTranslation->save();
            }
            return true;
        }else{
            return false;
        }
    }


}