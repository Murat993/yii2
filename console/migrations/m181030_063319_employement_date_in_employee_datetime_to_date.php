<?php

use yii\db\Migration;

/**
 * Handles the creation of table `update_ms_survey_filial`.
 */
class m181030_063319_employement_date_in_employee_datetime_to_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `employee` CHANGE `employment_date` `employment_date` DATE NOT NULL;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
