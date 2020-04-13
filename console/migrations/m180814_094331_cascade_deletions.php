<?php

use yii\db\Migration;

/**
 * Class m180813_094331_modify_table_ms_survey_column_ms_id_not_null
 */
class m180814_094331_cascade_deletions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `ms_survey` DROP FOREIGN KEY `ms_survey_fk1`; ALTER TABLE `ms_survey` ADD CONSTRAINT `ms_survey_fk1` FOREIGN KEY (`survey_filial`) REFERENCES `survey_filial`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `ms_survey_date` DROP FOREIGN KEY `fk-ms_survey_date-ms_survey_id`; ALTER TABLE `ms_survey_date` ADD CONSTRAINT `fk-ms_survey_date-ms_survey_id` FOREIGN KEY (`ms_survey_id`) REFERENCES `ms_survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `answer_option` DROP FOREIGN KEY `answer_option_fk0`; ALTER TABLE `answer_option` ADD CONSTRAINT `answer_option_fk0` FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `answer_option_lang` DROP FOREIGN KEY `answer_option_lang_fk0`; ALTER TABLE `answer_option_lang` ADD CONSTRAINT `answer_option_lang_fk0` FOREIGN KEY (`answer_option_id`) REFERENCES `answer_option`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `answer_option_lang` DROP FOREIGN KEY `answer_option_lang_fk1`; ALTER TABLE `answer_option_lang` ADD CONSTRAINT `answer_option_lang_fk1` FOREIGN KEY (`lang_id`) REFERENCES `lang`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `client_user` DROP FOREIGN KEY `client_user_fk1`; ALTER TABLE `client_user` ADD CONSTRAINT `client_user_fk1` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `client_user_filial` DROP FOREIGN KEY `client_user_filial_fk0`; ALTER TABLE `client_user_filial` ADD CONSTRAINT `client_user_filial_fk0` FOREIGN KEY (`client_user_id`) REFERENCES `client_user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `client_user_filial` DROP FOREIGN KEY `client_user_filial_fk1`; ALTER TABLE `client_user_filial` ADD CONSTRAINT `client_user_filial_fk1` FOREIGN KEY (`filial_id`) REFERENCES `filial`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `employee_filial` DROP FOREIGN KEY `employee_filial_fk0`; ALTER TABLE `employee_filial` ADD CONSTRAINT `employee_filial_fk0` FOREIGN KEY (`filial_id`) REFERENCES `filial`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `employee_filial` DROP FOREIGN KEY `employee_filial_fk1`; ALTER TABLE `employee_filial` ADD CONSTRAINT `employee_filial_fk1` FOREIGN KEY (`employee_id`) REFERENCES `employee`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `filial` DROP FOREIGN KEY `filial_fk0`; ALTER TABLE `filial` ADD CONSTRAINT `filial_fk0` FOREIGN KEY (`parent_id`) REFERENCES `client`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `filial_structure_unit` DROP FOREIGN KEY `filial_structure_unit_fk0`; ALTER TABLE `filial_structure_unit` ADD CONSTRAINT `filial_structure_unit_fk0` FOREIGN KEY (`parent_id`) REFERENCES `filial_structure_unit`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `filial_structure_unit` DROP FOREIGN KEY `filial_structure_unit_fk1`; ALTER TABLE `filial_structure_unit` ADD CONSTRAINT `filial_structure_unit_fk1` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `question` DROP FOREIGN KEY `question_fk0`; ALTER TABLE `question` ADD CONSTRAINT `question_fk0` FOREIGN KEY (`article_id`) REFERENCES `article`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `questionary` DROP FOREIGN KEY `questionary_fk1`; ALTER TABLE `questionary` ADD CONSTRAINT `questionary_fk1` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `question_answer` DROP FOREIGN KEY `question_answer_fk1`; ALTER TABLE `question_answer` ADD CONSTRAINT `question_answer_fk1` FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `question_answer` DROP FOREIGN KEY `question_answer_fk2`; ALTER TABLE `question_answer` ADD CONSTRAINT `question_answer_fk2` FOREIGN KEY (`survey_id`) REFERENCES `survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `question_check` DROP FOREIGN KEY `question_check_fk2`; ALTER TABLE `question_check` ADD CONSTRAINT `question_check_fk2` FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `question_lang` DROP FOREIGN KEY `question_lang_fk0`; ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_fk0` FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `question_lang` DROP FOREIGN KEY `question_lang_fk1`; ALTER TABLE `question_lang` ADD CONSTRAINT `question_lang_fk1` FOREIGN KEY (`lang_id`) REFERENCES `lang`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `survey` DROP FOREIGN KEY `survey_fk0`; ALTER TABLE `survey` ADD CONSTRAINT `survey_fk0` FOREIGN KEY (`questionary_id`) REFERENCES `questionary`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `survey` DROP FOREIGN KEY `survey_fk1`; ALTER TABLE `survey` ADD CONSTRAINT `survey_fk1` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `survey_filial` DROP FOREIGN KEY `survey_filial_fk0`; ALTER TABLE `survey_filial` ADD CONSTRAINT `survey_filial_fk0` FOREIGN KEY (`survey_id`) REFERENCES `survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `survey_filial` DROP FOREIGN KEY `survey_filial_fk1`; ALTER TABLE `survey_filial` ADD CONSTRAINT `survey_filial_fk1` FOREIGN KEY (`filial_id`) REFERENCES `filial`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `survey_lang` DROP FOREIGN KEY `survey_lang_fk0`; ALTER TABLE `survey_lang` ADD CONSTRAINT `survey_lang_fk0` FOREIGN KEY (`survey_id`) REFERENCES `survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `survey_lang` DROP FOREIGN KEY `survey_lang_fk1`; ALTER TABLE `survey_lang` ADD CONSTRAINT `survey_lang_fk1` FOREIGN KEY (`lang_id`) REFERENCES `lang`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `task` DROP FOREIGN KEY `task_fk0`; ALTER TABLE `task` ADD CONSTRAINT `task_fk0` FOREIGN KEY (`survey_id`) REFERENCES `survey`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `task_answer` DROP FOREIGN KEY `task_answer_fk1`; ALTER TABLE `task_answer` ADD CONSTRAINT `task_answer_fk1` FOREIGN KEY (`task_id`) REFERENCES `task`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
        $this->execute('ALTER TABLE `task_lang` DROP FOREIGN KEY `task_lang_fk0`; ALTER TABLE `task_lang` ADD CONSTRAINT `task_lang_fk0` FOREIGN KEY (`task_id`) REFERENCES `task`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `task_lang` DROP FOREIGN KEY `task_lang_fk1`; ALTER TABLE `task_lang` ADD CONSTRAINT `task_lang_fk1` FOREIGN KEY (`lang_id`) REFERENCES `lang`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180813_094331_modify_table_ms_survey_column_ms_id_not_null cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180813_094331_modify_table_ms_survey_column_ms_id_not_null cannot be reverted.\n";

        return false;
    }
    */
}
