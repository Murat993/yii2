<?php

use yii\db\Migration;

/**
 * Handles the creation of table `message`.
 */
class m181008_032509_create_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('message', [
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer(),
            'from' => $this->integer(),
            'text' => $this->text(),
            'read' => $this->integer(),
            'time' => $this->dateTime(),
        ]);
        $this->addForeignKey('fk_message_user','message','from','user', 'id','CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('message');
    }
}
