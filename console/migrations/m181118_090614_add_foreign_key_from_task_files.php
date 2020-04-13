<?php

use yii\db\Migration;

/**
 * Class m181118_090614_add_foreign_key_from_task_files
 */
class m181118_090614_add_foreign_key_from_task_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // add foreign key for table `task`
        $this->addForeignKey(
            'fk-task_file-ms-survey-id',
            'task_file',
            'ms_survey_id',
            'ms_survey',
            'id',
            'CASCADE'
        );
    }

}
