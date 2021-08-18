<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_182010_upload extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%upload}}', [
            'id' => $this->primaryKey(),
            'path' => $this->string(500)->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'user_id' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('upload_user_idx', '{{%upload}}', 'user_id');

        $this->addForeignKey('fk_upload_user',
            'upload', 'user_id',
            'user', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_upload_user', '{{%upload}}');

        $this->dropTable('{{%upload}}');
    }
}
