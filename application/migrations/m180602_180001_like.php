<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180602_180001_like extends Migration
{
    public function up()
    {
        $this->createTable('{{%like}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('like_from_user_idx', '{{%like}}', 'from_user_id');
        $this->createIndex('like_to_user_idx', '{{%like}}', 'to_user_id');
        $this->createIndex('like_from_to_user_idx', '{{%like}}', ['from_user_id', 'to_user_id'], true);

        $this->addForeignKey('fk_like_from_user',
            '{{%like}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_like_to_user',
            '{{%like}}', 'to_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_like_from_user', '{{%like}}');
        $this->dropForeignKey('fk_like_to_user', '{{%like}}');

        $this->dropTable('{{%like}}');
    }
}
