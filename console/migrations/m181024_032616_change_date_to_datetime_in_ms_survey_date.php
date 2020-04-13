<?php

use yii\db\Migration;

/**
 * Handles the creation of table `client_color`.
 */
class m181024_032616_change_date_to_datetime_in_ms_survey_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ms_survey_date` CHANGE `date` `date` DATETIME NULL DEFAULT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
