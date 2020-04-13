<?php

use yii\db\Migration;

/**
 * Class m181121_065015_add_unique_question_answer
 */
class m181121_065015_cascade_update_article_lang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
ALTER TABLE `article_lang` DROP FOREIGN KEY `article_lang_fk0`; 
ALTER TABLE `article_lang` ADD CONSTRAINT `article_lang_fk0` FOREIGN KEY (`article_id`) REFERENCES `article`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `article_lang` DROP FOREIGN KEY `article_lang_fk1`; 
ALTER TABLE `article_lang` ADD CONSTRAINT `article_lang_fk1` FOREIGN KEY (`lang_id`) REFERENCES `lang`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }
}
