<?php

use yii\db\Migration;

/**
 * Class m180828_085552_changelog_cascade_fix
 */
class m180828_085552_changelog_cascade_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `changelog` DROP FOREIGN KEY `changelog_fk0`; ALTER TABLE `changelog` ADD CONSTRAINT `changelog_fk0` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180828_085552_changelog_cascade_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180828_085552_changelog_cascade_fix cannot be reverted.\n";

        return false;
    }
    */
}
