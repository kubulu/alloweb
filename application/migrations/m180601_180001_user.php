<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_180001_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password_hash' => $this->string(60)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'confirmed_at' => $this->integer()->null(),
            'unconfirmed_email' => $this->string(255)->null(),
            'blocked_at' => $this->integer()->null(),
            'registration_ip' => $this->string(45)->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'flags' => $this->integer()->notNull()->defaultValue(0),
            'last_login_at' => $this->integer()->null(),
        ], $this->tableOptions);

        $this->createIndex('user_unique_username_idx', '{{%user}}', 'username', true);
        $this->createIndex('user_unique_email_idx', '{{%user}}', 'email', true);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
