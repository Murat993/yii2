<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_add_sorting_to_question extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question` ADD `sorting` INT NULL DEFAULT \'1\' AFTER `answer_type`;');
    }
}
