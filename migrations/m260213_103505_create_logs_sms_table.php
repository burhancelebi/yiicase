<?php

use yii\db\Migration;

class m260213_103505_create_logs_sms_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%logs_sms}}', [
            'id' => $this->primaryKey()->unsigned(),

            'parent_table' => "ENUM('cart_order','reservation','marketing_campaign') NULL",
            'parent_id' => $this->integer()->unsigned()->null(),

            'phone' => $this->string(100)->notNull(),
            'message' => $this->text()->notNull(),

            'priority' => $this->tinyInteger()->defaultValue(0),
            'device_id' => $this->string(255)->null(),
            'cost' => $this->float()->notNull()->defaultValue(0),

            'sent' => $this->tinyInteger()->unsigned()->defaultValue(0),
            'delivered' => $this->tinyInteger()->unsigned()->defaultValue(0),
            'error' => $this->text()->null(),

            'provider' => "ENUM(
                'inhousesms',
                'wholesalesms',
                'prowebsms',
                'onverify',
                'inhousesms-nz',
                'inhousesms-my',
                'inhousesms-au',
                'inhousesms-au-marketing',
                'inhousesms-nz-marketing'
            ) NOT NULL",

            'status' => $this->tinyInteger()->notNull()->defaultValue(0),

            'fetched_at' => $this->timestamp()->null(),
            'sent_at' => $this->timestamp()->null(),
            'delivered_at' => $this->timestamp()->null(),

            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),

            'send_after' => $this->timestamp()->null(),
            'time_zone' => $this->string(55)->null(),
        ]);

        // INDEXLER
        $this->createIndex(
            'IDX_logs_sms_provider_status_priority_id',
            '{{%logs_sms}}',
            ['provider', 'status', 'priority', 'id']
        );

        $this->createIndex(
            'IDX_logs_sms_created_at',
            '{{%logs_sms}}',
            'created_at'
        );

        $this->createIndex(
            'IDX_logs_sms_parent',
            '{{%logs_sms}}',
            ['parent_table', 'parent_id']
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%logs_sms}}');
    }
}