<?php

use yii\db\Migration;

/**
 * Class m180904_111605_add_reset_token_field_to_user_table
 */
class m180904_111605_add_reset_token_field_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'reset_token', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('user', 'reset_token');
    }
}
