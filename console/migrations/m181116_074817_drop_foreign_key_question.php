<?php

use yii\db\Migration;

/**
 * Class m181116_074817_ALTER_TABLE_question_check
 */
class m181116_074817_drop_foreign_key_question extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question_check` DROP FOREIGN KEY `question_check_ibfk_1`;');
        $this->execute('ALTER TABLE `question_check` DROP FOREIGN KEY `question_check_fk0`;');
    }

}
