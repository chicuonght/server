<?php


class m130524_201442_init extends \auth\rbac\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'user_id' => $this->primaryKey(11)->unsigned(),
            'username' => $this->string(12)->notNull()->unique(),
            'auth_key' => $this->string(64)->unique(),
            'password_hash' => $this->string(64)->notNull(),
            'password_reset_token' => $this->string(64)->unique(),
            'email' => $this->string(64)->notNull()->unique(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->unsigned(),
            'created_at' => $this->integer()->notNull()->unsigned()->notNull(),
            'updated_at' => $this->integer()->notNull()->unsigned()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
