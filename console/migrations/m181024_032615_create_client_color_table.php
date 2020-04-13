<?php

use yii\db\Migration;

/**
 * Handles the creation of table `client_color`.
 */
class m181024_032615_create_client_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('client_color', [
            'id' => $this->primaryKey(),
            'color' => $this->string(),
            'client_id' => $this->integer(),
            'procent' => $this->integer()
        ]);
        $this->addForeignKey('fk_client_color_client','client_color','client_id','client', 'id','CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('client_color');
    }
}
