<?php

use yii\db\Migration;

/**
 * Handles adding date_type to table `ms_survey`.
 */
class m180810_052616_add_date_type_column_to_ms_survey_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ms_survey', 'date_type', $this->integer());
        $this->addColumn('ms_survey', 'comment', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ms_survey', 'date_type');
        $this->dropColumn('ms_survey', 'comment');
    }
}
