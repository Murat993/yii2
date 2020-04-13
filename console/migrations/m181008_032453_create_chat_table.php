<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat`.
 */
class m181008_032453_create_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat', [
            'id' => $this->primaryKey(),
            'user_admin' => $this->integer(),
            'user_client' => $this->integer(),
            'ms_survey_id' => $this->integer(),
        ]);
        $this->addForeignKey('fk_chat_user_admin','chat','user_admin','user', 'id','CASCADE', 'CASCADE');
        $this->addForeignKey('fk_chat_user_client','chat','user_client','user', 'id','CASCADE', 'CASCADE');
        $this->addForeignKey('fk_chat_ms_survey','chat','ms_survey_id','ms_survey', 'id','CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('chat');
    }
}
