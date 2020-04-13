<?php

use yii\db\Migration;

/**
 * Handles the creation of table `update_ms_survey_filial`.
 */
class m181030_063318_create_update_ms_survey_filial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ms_survey_date` CHANGE `employee_name` `employee_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
