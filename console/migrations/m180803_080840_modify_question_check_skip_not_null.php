<?php

use yii\db\Migration;

/**
 * Class m180803_080840_modify_question_check_skip_not_null
 */
class m180803_080840_modify_question_check_skip_not_null extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE question_check MODIFY answer_id INT(11);");
        $this->execute("ALTER TABLE question_check MODIFY skip TINYINT(11);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180803_080840_modify_question_check_skip_not_null cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180803_080840_modify_question_check_skip_not_null cannot be reverted.\n";

        return false;
    }
    */
}
