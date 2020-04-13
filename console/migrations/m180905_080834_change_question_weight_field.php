<?php

use yii\db\Migration;

/**
 * Class m180903_080834_change_answer_option_points_field
 */
class m180905_080834_change_question_weight_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `question` CHANGE `weight` `weight` FLOAT NOT NULL;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180905_080834_change_question_weight_field cannot be reverted.\n";

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
