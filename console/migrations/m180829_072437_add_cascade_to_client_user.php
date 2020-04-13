<?php

use yii\db\Migration;

/**
 * Class m180829_072437_add_cascade_to_client_user
 */
class m180829_072437_add_cascade_to_client_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `client_user` DROP FOREIGN KEY `client_user_fk0`; ALTER TABLE `client_user` ADD CONSTRAINT `client_user_fk0` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180829_072437_add_cascade_to_client_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180829_072437_add_cascade_to_client_user cannot be reverted.\n";

        return false;
    }
    */
}
