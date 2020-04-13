<?php

use yii\db\Migration;

/**
 * Handles adding instruction to table `survey_fFilial`.
 */
class m181120_070649_add_instruction_column_to_survey_filial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('survey_filial', 'instruction', $this->string(260));
    }

}
