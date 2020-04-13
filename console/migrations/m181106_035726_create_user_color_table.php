<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_color`.
 */
class m181106_035726_create_user_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_color', [
            'id' => $this->primaryKey(),
            'color' => $this->string(),
            'user_id' => $this->integer(),
            'procent' => $this->integer()
        ]);
        $this->addForeignKey('fk_user_color_user','user_color','user_id','user', 'id','CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_color');
    }
}
