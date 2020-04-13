<?php

use yii\db\Migration;

/**
 * Class m180813_094331_modify_table_ms_survey_column_ms_id_not_null
 */
class m180813_094331_modify_table_ms_survey_column_ms_id_not_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE ms_survey MODIFY ms_id INT(11);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180813_094331_modify_table_ms_survey_column_ms_id_not_null cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180813_094331_modify_table_ms_survey_column_ms_id_not_null cannot be reverted.\n";

        return false;
    }
    */
}
