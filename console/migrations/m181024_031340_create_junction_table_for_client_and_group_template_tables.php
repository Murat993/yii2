<?php

use yii\db\Migration;

/**
 * Handles the creation of table `client_group_template`.
 * Has foreign keys to the tables:
 *
 * - `client`
 * - `group_template`
 */
class m181024_031340_create_junction_table_for_client_and_group_template_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('client_group_template', [
            'client_id' => $this->integer(),
            'group_template_id' => $this->integer(),
            'PRIMARY KEY(client_id, group_template_id)',
        ]);

        // creates index for column `client_id`
        $this->createIndex(
            'idx-client_group_template-client_id',
            'client_group_template',
            'client_id'
        );

        // add foreign key for table `client`
        $this->addForeignKey(
            'fk-client_group_template-client_id',
            'client_group_template',
            'client_id',
            'client',
            'id',
            'CASCADE'
        );

        // creates index for column `group_template_id`
        $this->createIndex(
            'idx-client_group_template-group_template_id',
            'client_group_template',
            'group_template_id'
        );

        // add foreign key for table `group_template`
        $this->addForeignKey(
            'fk-client_group_template-group_template_id',
            'client_group_template',
            'group_template_id',
            'group_template',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `client`
        $this->dropForeignKey(
            'fk-client_group_template-client_id',
            'client_group_template'
        );

        // drops index for column `client_id`
        $this->dropIndex(
            'idx-client_group_template-client_id',
            'client_group_template'
        );

        // drops foreign key for table `group_template`
        $this->dropForeignKey(
            'fk-client_group_template-group_template_id',
            'client_group_template'
        );

        // drops index for column `group_template_id`
        $this->dropIndex(
            'idx-client_group_template-group_template_id',
            'client_group_template'
        );

        $this->dropTable('client_group_template');
    }
}
