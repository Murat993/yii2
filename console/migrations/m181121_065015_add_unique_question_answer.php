<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_add_unique_question_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question_answer` ADD UNIQUE( `question_id`, `ms_survey_id`);');
    }
}
