<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180606_180001_user_premium extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_premium}}', [
            'user_id' => $this->primaryKey(),
            'premium_until' => $this->integer(),
            'incognito_active' => $this->boolean()->defaultValue(false),
            'show_online_status' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('user_premium_user_idx', '{{%user_premium}}', 'user_id');

        $this->addForeignKey('fk_user_premium_user',
            '{{%user_premium}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_premium_user', '{{%user_premium}}');

        $this->dropTable('{{%user_premium}}');
    }
}
