<?php

use yii\db\Migration;

/**
 * Handles adding ms_survey_id to table `question_answer`.
 */
class m181005_044830_add_ms_survey_id_column_to_question_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE question_answer ADD `ms_survey_id` INT(11);");
        $this->execute("ALTER TABLE question_answer ADD FOREIGN KEY (`ms_survey_id`)"
            ." REFERENCES ms_survey(id);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('question_answer', 'ms_survey_id');
    }
}
