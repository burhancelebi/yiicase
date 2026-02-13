<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class MobileController extends Controller
{
    public function actionPopulateRandomData()
    {
        $db = Yii::$app->db;
        echo "Truncating table...\n";
        $db->createCommand()->truncateTable('logs_sms')->execute();

        $timeZones = [
            'Australia/Melbourne','Australia/Sydney','Australia/Brisbane',
            'Australia/Adelaide','Australia/Perth','Australia/Tasmania',
            'Pacific/Auckland','Asia/Kuala_Lumpur','Europe/Istanbul',
        ];

        $now = time();

        $insertBatch = function ($count, $status) use ($db, $timeZones, $now) {

            $rows = [];

            for ($i = 0; $i < $count; $i++) {

                $phone = '04' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                $messageLength = rand(100, 255);
                $message = substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz ', 10)), 0, $messageLength);

                $timeZone = $timeZones[array_rand($timeZones)];
                $localHour = (int) (new \DateTime('now', new \DateTimeZone($timeZone)))->format('G');

                $sendAfter = null;
                if ($status == 0) {
                    $sendAfter = date('Y-m-d H:i:s', rand($now - 7200, $now + 172800));
                }

                $rows[] = [
                    'phone' => $phone,
                    'message' => $message,
                    'priority' => 0,
                    'cost' => 0,
                    'sent' => 0,
                    'delivered' => 0,
                    'provider' => 'inhousesms',
                    'status' => $status,
                    'send_after' => $sendAfter,
                    'time_zone' => $timeZone,
                    'local_send_hour' => $localHour,
                ];
            }

            $db->createCommand()->batchInsert(
                'logs_sms',
                [
                    'phone','message','priority','cost','sent','delivered',
                    'provider','status','send_after','time_zone','local_send_hour'
                ],
                $rows
            )->execute();
        };

        echo "Inserting status=1 rows...\n";
        for ($i = 0; $i < 1000; $i++) {
            $insertBatch(1000, 1);
            echo ".";
        }

        echo "\nInserting status=0 rows...\n";
        for ($i = 0; $i < 50; $i++) {
            $insertBatch(1000, 0);
            echo ".";
        }

        echo "\nDone.\n";
    }

    public function actionGetMessagesToSend()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $rows = $db->createCommand(
                "SELECT *
                FROM logs_sms
                WHERE status = 0
                AND provider = 'inhousesms'
                AND send_after < NOW()
                AND local_send_hour BETWEEN 9 AND 23
                ORDER BY id ASC
                LIMIT 5
                FOR UPDATE SKIP LOCKED")->queryAll();

            if (!empty($rows)) 
            {
                $ids = array_column($rows, 'id');
                $db->createCommand()->update(
                    'logs_sms',
                    [
                        'status' => 1,
                        'sent' => 1,
                        'sent_at' => new \yii\db\Expression('NOW()')
                    ],
                    ['id' => $ids]
                )->execute();

                $transaction->commit();

                print_r($rows);
            }
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}