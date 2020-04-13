<?php

use yii\db\Migration;

/**
 * Handles adding resub_token to table `client`.
 */
class m190124_030344_add_resub_token_column_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('client', 'resubscribe_token', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('client', 'resubscribe_token');
    }
}
