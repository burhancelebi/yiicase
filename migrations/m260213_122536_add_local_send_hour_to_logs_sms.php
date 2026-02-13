<?php

use yii\db\Migration;

class m260213_122536_add_local_send_hour_to_logs_sms extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('logs_sms', 'local_send_hour', $this->tinyInteger()->null());

        $this->createIndex(
            'idx_fetch',
            'logs_sms',
            ['status', 'provider', 'send_after', 'local_send_hour', 'id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_fetch', 'logs_sms');
        $this->dropColumn('logs_sms', 'local_send_hour');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260213_122536_add_local_send_hour_to_logs_sms cannot be reverted.\n";

        return false;
    }
    */
}
