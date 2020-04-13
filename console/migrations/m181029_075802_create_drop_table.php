<?php

use yii\db\Migration;

/**
 * Handles the creation of table `drop`.
 */
class m181029_075802_create_drop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('client_color');
    }

}
