<?php

use yii\db\Migration;

/**
 * Class m180903_080834_change_answer_option_points_field
 */
class m180903_080834_change_answer_option_points_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `answer_option` CHANGE `points` `points` FLOAT NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180903_080834_change_answer_option_points_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180903_080834_change_answer_option_points_field cannot be reverted.\n";

        return false;
    }
    */
}
