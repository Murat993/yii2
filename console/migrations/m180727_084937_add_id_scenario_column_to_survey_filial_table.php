<?php

use yii\db\Migration;

/**
 * Handles adding id_scenario to table `survey_filial`.
 */
class m180727_084937_add_id_scenario_column_to_survey_filial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('survey_filial', 'id_scenario', $this->integer());

        $this->createIndex('idx-survey_filial-id_scenario', 'survey_filial', 'id_scenario');
        $this->addForeignKey(
            'fk-survey_filial-id_scenario', 'survey_filial', 'id_scenario', 'scenario', 'id', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('survey_filial', 'id_scenario');
        $this->dropIndex('idx-survey_filial-id_scenario', 'survey_filial');
        $this->dropForeignKey('fk-survey_filial-id_scenario', 'survey_filial');
    }
}
