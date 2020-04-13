<?php

use yii\db\Migration;

/**
 * Class m181101_060251_create_position_column_to_filial
 */
class m181101_060251_create_position_column_to_filial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('filial', 'position_id', $this->integer()->defaultValue(null));
        $this->addForeignKey('fk_filial_position','filial','position_id','position', 'id','CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('filial','position_id');
    }

}
