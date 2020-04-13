<?php

use yii\db\Migration;

/**
 * Class m180809_073015_add_column_text_answer_to_question_check
 */
class m180809_073015_add_column_text_answer_to_question_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE question_check ADD `text_answer` VARCHAR(1024);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180809_073015_add_column_text_answer_to_question_check cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180809_073015_add_column_text_answer_to_question_check cannot be reverted.\n";

        return false;
    }
    */
}
