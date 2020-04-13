<?php

use yii\db\Migration;

/**
 * Class m180806_040542_add_answer_option_id_to_question_check
 */
class m180806_040542_add_answer_option_id_to_question_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE question_check ADD `answer_option_id` INT(11);");
        $this->execute("ALTER TABLE question_check ADD FOREIGN KEY (`answer_option_id`)"
            ." REFERENCES answer_option(id);");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180806_040542_add_answer_option_id_to_question_check cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180806_040542_add_answer_option_id_to_question_check cannot be reverted.\n";

        return false;
    }
    */
}
