<?php

use yii\db\Migration;

/**
 * Class m180810_030722_add_primary_key_to_task_lang_table
 */
class m180810_030722_add_primary_key_to_task_lang_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addPrimaryKey('task-lang_pk', 'task_lang', ['task_id', 'lang_id']);
        $this->addPrimaryKey('questionary-lang_pk', 'questionary_lang', ['questionary_id', 'lang_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('task-lang_pk', 'task_lang');
        $this->dropPrimaryKey('questionary-lang_pk', 'questionary_lang');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180810_030722_add_primary_key_to_task_lang_table cannot be reverted.\n";

        return false;
    }
    */
}
