<?php

use yii\db\Migration;

/**
 * Handles adding notified to table `client`.
 */
class m190123_032818_add_notified_column_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('client', 'notified', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('client', 'notified');
    }
}
