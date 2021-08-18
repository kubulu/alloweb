<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180917_190001_notification extends Migration
{
    public function up()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey(),
            'class' => $this->string(100)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'sender_user_id' => $this->integer(),
            'is_viewed' => $this->boolean()->defaultValue(false)->notNull(),
            'source_class' => $this->string(100),
            'source_pk' => $this->integer(),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('notification_user_idx', '{{%notification}}', 'user_id');
        $this->createIndex('notification_sender_user_idx', '{{%notification}}', 'sender_user_id');
        $this->createIndex('notification_is_viewed', '{{%notification}}', 'is_viewed');

        $this->addForeignKey('fk_notification_user',
            '{{%notification}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_notification_sender_user',
            '{{%notification}}', 'sender_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_notification_user', '{{%notification}}');
        $this->dropForeignKey('fk_notification_sender_user', '{{%notification}}');

        $this->dropTable('{{%notification}}');
    }
}
