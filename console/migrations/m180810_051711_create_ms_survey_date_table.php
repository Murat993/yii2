<?php

use yii\db\Migration;

/**
 * Handles the creation of table `ms_survey_date`.
 */
class m180810_051711_create_ms_survey_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ms_survey_date', [
            'id' => $this->primaryKey(),
            'date' => $this->date(),
            'type' => $this->integer(),
            'ms_survey_id' => $this->integer()
        ]);


        $this->createIndex('idx-ms_survey_date-ms_survey_id', 'ms_survey_date', 'ms_survey_id');
        $this->addForeignKey(
            'fk-ms_survey_date-ms_survey_id', 'ms_survey_date', 'ms_survey_id', 'ms_survey', 'id', 'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-ms_survey_date-ms_survey_id', 'ms_survey_date');
        $this->dropIndex('idx-ms_survey_date-ms_survey_id', 'ms_survey_date');
        $this->dropTable('ms_survey_date');

    }
}
