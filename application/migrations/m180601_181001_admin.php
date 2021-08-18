<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_181001_admin extends Migration
{
    public function up()
    {
        $this->createTable('{{%admin}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('admin_idx', '{{%admin}}', 'user_id', true);

        $this->addForeignKey('fk_admin_user',
            '{{%admin}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_admin_user', '{{%admin}}');

        $this->dropTable('{{%admin}}');
    }
}
