<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180602_190001_message extends Migration
{
    public function up()
    {
        $this->createTable('{{%message}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'text' => $this->string(1000)->notNull()->append('COLLATE utf8mb4_bin'),
            'is_new' => $this->boolean()->defaultValue(true),
            'is_deleted_by_sender' => $this->boolean()->defaultValue(false),
            'is_deleted_by_receiver' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('message_from_user_idx', '{{%message}}', 'from_user_id');
        $this->createIndex('message_to_user_idx', '{{%message}}', 'to_user_id');
        $this->createIndex('message_is_new_idx', '{{%message}}', 'is_new');
        $this->createIndex('message_is_deleted_by_sender_idx', '{{%message}}', 'is_deleted_by_sender');
        $this->createIndex('message_is_deleted_by_receiver_idx', '{{%message}}', 'is_deleted_by_receiver');

        $this->addForeignKey('fk_message_from_user',
            '{{%message}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_message_to_user',
            '{{%message}}', 'to_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_message_from_user', '{{%message}}');
        $this->dropForeignKey('fk_message_to_user', '{{%message}}');

        $this->dropTable('{{%message}}');
    }
}
