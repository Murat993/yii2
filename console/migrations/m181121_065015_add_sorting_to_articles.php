<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_add_sorting_to_articles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `article` ADD `sorting` INT NULL DEFAULT \'1\' AFTER `level`;');
    }
}
