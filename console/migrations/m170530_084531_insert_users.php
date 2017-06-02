<?php

use yii\db\Migration;

class m170530_084531_insert_users extends Migration
{
    public $table = '{{%user%}}';

    public function up()
    {
        $time = time();
        $this->batchInsert($this->table,
            [
                'user_id',
                'username',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'email',
                'status',
                'created_at',
                'updated_at',
            ], [
                [
                    'user_id' => 1,
                    'username' => 'admin',
                    'auth_key' => Yii::$app->security->generateRandomString(),
                    'password_hash' => Yii::$app->security->generateRandomString(),
                    'password_reset_token' => Yii::$app->security->generateRandomString(),
                    'email' => 'thanh.pham@seldatinc.com',
                    'status' => 1,
                    'created_at' => $time,
                    'updated_at' => $time,
                ],
                [
                    'user_id' => 2,
                    'username' => 'author',
                    'auth_key' => Yii::$app->security->generateRandomString(),
                    'password_hash' => Yii::$app->security->generateRandomString(),
                    'password_reset_token' => Yii::$app->security->generateRandomString(),
                    'email' => 'thanhpv@vietta.vn',
                    'status' => 1,
                    'created_at' => $time,
                    'updated_at' => $time,
                ],


            ]);

    }

    public function down()
    {
        $this->delete($this->table, ['user_id' => [1, 2]]);


    }


}
