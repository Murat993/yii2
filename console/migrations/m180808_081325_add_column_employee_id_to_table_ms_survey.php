<?php

use yii\db\Migration;

/**
 * Class m180808_081325_add_column_employee_id_to_table_ms_survey
 */
class m180808_081325_add_column_employee_id_to_table_ms_survey extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE ms_survey ADD `employee_id` INT(11);");
        $this->execute("ALTER TABLE ms_survey ADD FOREIGN KEY (`employee_id`)"
            ." REFERENCES employee(id);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180808_081325_add_column_employee_id_to_table_ms_survey cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180808_081325_add_column_employee_id_to_table_ms_survey cannot be reverted.\n";

        return false;
    }
    */
}
