<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 29.06.18
 * Time: 16:27
 */

namespace common\services;


use common\models\AnswerOption;
use common\models\Article;
use common\models\ArticleLang;
use common\models\Question;
use common\models\Questionary;
use common\translate\models\Lang;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;

class QuestService
{

    public function getQuestion($id)
    {
        return Question::findOne(['id' => $id]);
    }

    public function getQuestionsCount($id)
    {
        $query = Question::find();
        $query->innerJoin('article', "question.article_id = article.id");
        $query->innerJoin('questionary', "article.questionary = questionary.id");
        $query->where([
            'questionary.id' => $id
        ]);
        return $query->count();
    }

    public function getQuestionary($id)
    {
        return Questionary::findOne(['id' => $id]);
    }

    public function getQuestionaries()
    {
        return Questionary::find()->all();
    }

    public function getQuestionariesAsMap()
    {
        $questionaries = $this->getQuestionaries();
        if ($questionaries) {
            return ArrayHelper::map($questionaries, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getQuestionariesMapByClient($id)
    {
        $questionaries = Questionary::find()->where(['client_id' => $id])->all();
        if ($questionaries) {
            return ArrayHelper::map($questionaries, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getQuestionsListByMsSupervisorArticle($article_id, $ms_survey_id, $supervisor_id)
    {
        return Question::find()
            ->where(['article_id' => $article_id])
            ->with([
                'currentTranslate', // loadabing data associated with the current translation
                'hasTranslate', // need to display the status was translated page
                'questionAnswer.questionCheck',
            ])
            ->joinWith(['questionAnswer' => function ($query) use ($ms_survey_id) {
                $query->onCondition(['ms_survey_id' => $ms_survey_id])->from(['qa1' => 'question_answer']);
            }])
            ->all();
    }

    public function getQuestionsMapByClient(int $id, int $article_id = null)
    {
        $query = Question::find()->
        joinWith('article.questionary')->andWhere(['client_id' => $id]);
        $result = [];
        if ($article_id) {
            $query->andWhere(['article_id' => $article_id]);
        }
        $questions = $query->all();
        foreach ($questions as $item) {
            $result[$item->id] = $item->name . " ($item->code)";
        }
        return $result;
    }


    public function prepareModel($model)
    {
        preg_match('/(?<=tag:)(.*)/', $model->name, $tag);
        if ($tag) {
            $model->name = $tag[0];
        }
        if ((int)$model->name) {
            $question = $this->getQuestion($model->name);
            $model->name = $question->name;
            $model->code = $question->code;
        }
        if ((int)$model->move_before || (int)$model->move_after) {
            $model = $this->manageSorting($model);
        } else {
            $model->sorting = Question::find()->where(['article_id' => $model->article_id])->count() + 1;
        }
        return $model;
    }


    private function manageSorting($model)
    {
        if ($model->move_before !== '') {
            $questBefore = $this->getQuestion($model->move_before);
            $model->sorting = $questBefore->sorting - 1;
            return $model;
        } elseif ($model->move_after !== '') {
            $questAfter = $this->getQuestion($model->move_after);
            $model->sorting = $questAfter->sorting + 1;
            return $model;
        }
        return $model;
    }

    public function copyQuestionary($id)
    {
        $questionary = $this->getQuestionary($id);
        if ($questionary) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $newQuestionary = new Questionary();
                $newQuestionary->attributes = $questionary->attributes;
                $newQuestionary->name = $questionary->name ? $questionary->name : '';
                $newQuestionary->save();
                if ($this->copyChilds($questionary->id, $newQuestionary->id)) {
                    $transaction->commit();
                    return $newQuestionary->id;
                }

            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
    }

    public function copyQuestions($oldID, $newID)
    {
        $questions = Question::findAll(['article_id' => $oldID]);
        if ($questions) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($questions as $question) {
                    $newQuestion = new Question();
                    $newQuestion->attributes = $question->attributes;
                    $newQuestion->name = $question->name ? $question->name : '';
                    $newQuestion->article_id = $newID;
                    $newQuestion->code = $question->code;
                    if ($newQuestion->save()) {
                        Yii::$app->langService->cloneTranslations($question->id, $newQuestion->id, LangService::QUESTION);
                        $this->copyAnswerOptions($question->id, $newQuestion->id);
                    }
                }
                $transaction->commit();
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return true;
        }
    }

    private function copyAnswerOptions($oldID, $newID)
    {
        $options = AnswerOption::findAll(['question_id' => $oldID]);
        if ($options) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($options as $option) {
                    $newOption = new AnswerOption();
                    $newOption->attributes = $option->attributes;
                    $newOption->text = $option->text ? $option->text : '';
                    $newOption->question_id = $newID;
                    if ($newOption->save()) {
                        Yii::$app->langService->cloneTranslations($option->id, $newOption->id, LangService::ANSWER_OPTION);
                    }
                }
                $transaction->commit();
                return true;
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return true;
        }
    }


    private function copyChilds($oldID, $newID)
    {
        $translations = Yii::$app->langService->cloneTranslations($oldID, $newID, LangService::QUESTIONARY);
        $articles = Yii::$app->articleService->cloneArticles($oldID, $newID);
        if ($translations && $articles) {
            return true;
        } else {
            return false;
        }
    }
}