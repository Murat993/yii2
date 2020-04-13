<?php

use yii\db\Migration;

/**
 * Handles the creation of table `gloabal_color`.
 */
class m181029_075818_create_gloabal_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('global_color', [
            'id' => $this->primaryKey(),
            'color' => $this->string(),
            'procent' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('global_color');
    }
}
