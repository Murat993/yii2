<?php

use yii\db\Migration;

/**
 * Handles the creation of table `client_color`.
 */
class m181024_032617_add_employeee_name_to_mssurvey_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ms_survey_date` ADD `employee_name` VARCHAR(255) NOT NULL AFTER `ms_survey_id`;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
