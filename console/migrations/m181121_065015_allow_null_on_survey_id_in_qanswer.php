<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_allow_null_on_survey_id_in_qanswer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question_answer` CHANGE `survey_id` `survey_id` INT(11) NULL;');
    }
}
