<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 29.06.18
 * Time: 17:01
 */

namespace common\services;


use common\models\Article;
use common\models\Question;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use Yii;

class ArticleService
{
    public function getArticlesAsMap($id, $parent_id = null)
    {
        $articles = Article::find()->where(['questionary' => $id, 'parent_id' => $parent_id])->all();
        if ($articles) {
            return ArrayHelper::map($articles, 'id', 'name');
        } else {
            return [];
        }
    }

    public function getArticle($id)
    {
        return Article::findOne(['id' => $id]);
    }

    public function prepareModel($model)
    {
        preg_match('/(?<=tag:)(.*)/', $model->name, $tag);
        if ($tag) {
            $model->name = $tag[0];
            $model->code = null;
        }
        if ((int)$model->name) {
            $article = $this->getArticle($model->name);
            $model->name = $article->name;
            $model->code = $article->code;
        }
        if ((int)$model->move_before || (int)$model->move_after) {
            $model = $this->manageSorting($model);
        } else {
            $model->sorting = Article::find()
                    ->where(['parent_id' => $model->parent_id, 'questionary' => $model->questionary])
                    ->count() + 1;
        }

        return $model;
    }

    private function manageSorting($model)
    {
        if ($model->move_before !== '') {
            $articleBefore = $this->getArticle($model->move_before);
            $model->sorting = $articleBefore->sorting - 1;
            return $model;
        } elseif ($model->move_after !== '') {
            $articleAfter = $this->getArticle($model->move_after);
            $model->sorting = $articleAfter->sorting + 1;
            return $model;
        } else {

        }
        return $model;
    }

    private function getArticleByPosition(int $position)
    {
        return Article::find()->where(['sorting' => $position])->all();
    }

    public function getClientArticles($id)
    {
        return Article::find()->joinWith('questionary')->where(['client_id' => $id])->all();
    }

    public function getClientArticlesAsMap($id)
    {
        $articles = $this->getClientArticles($id);
        if ($articles) {
            return ArrayHelper::map($articles, 'id', 'name');
        } else {
            return [];
        }
    }

    public function cloneArticles($oldID, $newID)
    {
        $articles = Article::find()->where(['questionary' => $oldID])->andWhere(['parent_id' => null])->all();
        if ($articles) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($articles as $article) {
                    $newArticle = new Article();
                    $newArticle->attributes = $article->attributes;
                    $newArticle->questionary = $newID;
                    $newArticle->name = $article->name ? $article->name : '';
                    if ($newArticle->save()) {
                        $childs = $article->hasChilds();
                        if ($childs) {
                            $this->cloneArticleChilds($childs, $newID, $newArticle->id);
                        }
                        Yii::$app->langService->cloneTranslations($article->id, $newArticle->id, LangService::ARTICLE);
                        Yii::$app->questService->copyQuestions($article->id, $newArticle->id);
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

    public function cloneArticleChilds($childArticles, $newQuestID, $newArticleID)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($childArticles as $article) {
                $newArticle = new Article();
                $newArticle->attributes = $article->attributes;
                $newArticle->questionary = $newQuestID;
                $newArticle->name = $article->name ? $article->name : '';
                $newArticle->parent_id = $newArticleID;
                if ($newArticle->save()) {
                    $childs = $article->hasChilds();
                    if ($childs) {
                        $this->cloneArticleChilds($childs, $newQuestID, $newArticle->id);
                    }
                    Yii::$app->langService->cloneTranslations($article->id, $newArticle->id, LangService::ARTICLE);
                    Yii::$app->questService->copyQuestions($article->id, $newArticle->id);
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


}