<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180602_210001_block extends Migration
{
    public function up()
    {
        $this->createTable('{{%block}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'blocked_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('block_from_user_idx', '{{%block}}', 'from_user_id');
        $this->createIndex('block_blocked_user_idx', '{{%block}}', 'blocked_user_id');
        $this->createIndex('block_unique_idx', '{{%block}}', ['from_user_id', 'blocked_user_id'], true);

        $this->addForeignKey('fk_block_from_user',
            '{{%block}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_block_blocked_user',
            '{{%block}}', 'blocked_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_block_from_user', '{{%block}}');
        $this->dropForeignKey('fk_block_blocked_user', '{{%block}}');

        $this->dropTable('{{%block}}');
    }
}
