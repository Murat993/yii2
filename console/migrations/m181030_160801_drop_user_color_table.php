<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `user_color`.
 */
class m181030_160801_drop_user_color_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('user_color');
    }

}
