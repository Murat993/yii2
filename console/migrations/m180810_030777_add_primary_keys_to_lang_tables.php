<?php

use yii\db\Migration;

/**
 * Class m180810_030723_add_primary_key_to_task_lang_table
 */
class m180810_030777_add_primary_keys_to_lang_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addPrimaryKey('answer_option-lang_pk', 'answer_option_lang', ['answer_option_id', 'lang_id']);
        $this->addPrimaryKey('article-lang_pk', 'article_lang', ['article_id', 'lang_id']);
        $this->addPrimaryKey('question-lang_pk', 'question_lang', ['question_id', 'lang_id']);
        $this->addPrimaryKey('survey-lang_pk', 'survey_lang', ['survey_id', 'lang_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('answer_option-lang_pk', 'task_lang');
        $this->dropPrimaryKey('article-lang_pk', 'task_lang');
        $this->dropPrimaryKey('question-lang_pk', 'task_lang');
        $this->dropPrimaryKey('survey-lang_pk', 'task_lang');
    }

}
