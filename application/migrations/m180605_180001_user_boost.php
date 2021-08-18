<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180605_180001_user_boost extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_boost}}', [
            'user_id' => $this->primaryKey(),
            'boosted_at' => $this->integer(),
            'boosted_until' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('user_boost_user_idx', '{{%user_boost}}', 'user_id');

        $this->addForeignKey('fk_user_boost_user',
            '{{%user_boost}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_boost_user', '{{%user_boost}}');

        $this->dropTable('{{%user_boost}}');
    }
}
