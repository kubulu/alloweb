<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_182001_photo extends Migration
{
    public function up()
    {
        $this->createTable('{{%photo}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'source' => $this->string(500)->notNull(),
            'width' => $this->integer()->null(),
            'height' => $this->integer()->null(),
            'is_verified' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('photo_user_idx', '{{%photo}}', 'user_id');

        $this->addForeignKey('fk_photo_user',
            '{{%photo}}', 'user_id',
            'user', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_photo_user', '{{%photo}}');

        $this->dropTable('{{%photo}}');
    }
}
