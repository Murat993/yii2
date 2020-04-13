<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

/**
 * Class m180712_100338_create_table_scenario
 */
class m180712_100338_create_table_scenario extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('scenario', [
            'id' => $this->primaryKey(),
            'name' => Schema::TYPE_STRING,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('scenario');
    }
}
