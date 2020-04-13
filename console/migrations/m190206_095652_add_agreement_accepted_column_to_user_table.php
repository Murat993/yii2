<?php

use yii\db\Migration;

/**
 * Handles adding agreement_accepted to table `{{%user}}`.
 */
class m190206_095652_add_agreement_accepted_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'agreement_accepted', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'agreement_accepted');
    }
}
