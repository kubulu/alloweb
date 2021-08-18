<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180603_180001_guest extends Migration
{
    public function up()
    {
        $this->createTable('{{%guest}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'visited_user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('guest_from_user_idx', '{{%guest}}', 'from_user_id');
        $this->createIndex('guest_visited_user_idx', '{{%guest}}', 'visited_user_id');

        $this->addForeignKey('fk_guest_from_user',
            '{{%guest}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_guest_visited_user',
            '{{%guest}}', 'visited_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_guest_from_user', '{{%guest}}');
        $this->dropForeignKey('fk_guest_visited_user', '{{%guest}}');

        $this->dropTable('{{%guest}}');
    }
}
