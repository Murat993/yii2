<?php

use yii\db\Migration;

/**
 * Handles adding id_client to table `scenario`.
 */
class m180727_085428_add_id_client_column_to_scenario_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('scenario', 'id_client', $this->integer());

        $this->createIndex('idx-scenario-id_client', 'scenario', 'id_client');
        $this->addForeignKey(
            'fk-scenario-id_client', 'scenario', 'id_client', 'client', 'id', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('scenario', 'id_client');
        $this->dropIndex('idx-scenario-id_client', 'scenario');
        $this->dropForeignKey('fk-scenario-id_client', 'scenario');
    }
}
