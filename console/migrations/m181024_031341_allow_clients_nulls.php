<?php

use yii\db\Migration;

/**
 * Handles the creation of table `client_group_template`.
 * Has foreign keys to the tables:
 *
 * - `client`
 * - `group_template`
 */
class m181024_031341_allow_clients_nulls extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `client` CHANGE `group_id` `group_id` INT(11) NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
