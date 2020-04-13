<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_mssurvey_constraints extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question_answer` DROP FOREIGN KEY `question_answer_ibfk_1`; ALTER TABLE `question_answer` ADD CONSTRAINT `question_answer_ibfk_1` FOREIGN KEY (`ms_survey_id`) REFERENCES `ms_survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }
}
