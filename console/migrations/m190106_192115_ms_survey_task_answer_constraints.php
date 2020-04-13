<?php

use yii\db\Migration;

/**
 * Class m190106_192115_ms_survey_task_answer_constraints
 */
class m190106_192115_ms_survey_task_answer_constraints extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `task_answer` DROP FOREIGN KEY `task_answer_ibfk_1`; ALTER TABLE `task_answer` ADD CONSTRAINT `task_answer_ibfk_1` FOREIGN KEY (`ms_survey_id`) REFERENCES `ms_survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }
}
