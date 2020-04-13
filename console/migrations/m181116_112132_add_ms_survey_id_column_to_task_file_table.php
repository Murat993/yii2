<?php

use yii\db\Migration;

/**
 * Handles adding ms_survey_id to table `task_file`.
 */
class m181116_112132_add_ms_survey_id_column_to_task_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task_file', 'ms_survey_id', $this->integer());
    }

}
