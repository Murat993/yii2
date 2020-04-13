<?php

use yii\db\Migration;

/**
 * Class m190205_110422_change_question_comment_length
 */
class m190205_110422_change_question_comment_length extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `question_lang` CHANGE `comment` `comment` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190205_110422_change_question_comment_length cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190205_110422_change_question_comment_length cannot be reverted.\n";

        return false;
    }
    */
}
