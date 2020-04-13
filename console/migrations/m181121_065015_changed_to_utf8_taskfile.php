<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_changed_to_utf8_taskfile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `task_file` CHANGE `file_name` `file_name` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }
}
