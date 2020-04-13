<?php

use yii\db\Migration;

/**
 * Class m190129_083916_change_datetime_to_date_in_survey_table
 */
class m190129_083916_change_datetime_to_date_in_survey_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `survey` CHANGE `report_date` `report_date` DATE NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190129_083916_change_datetime_to_date_in_survey_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190129_083916_change_datetime_to_date_in_survey_table cannot be reverted.\n";

        return false;
    }
    */
}
