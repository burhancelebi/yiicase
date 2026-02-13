<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs_sms}}`.
 */
class m260213_103505_create_logs_sms_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logs_sms}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%logs_sms}}');
    }
}
