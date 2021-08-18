<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_120001_settings extends Migration
{
    public function up()
    {
        $this->createTable('{{%setting}}', [
            'category' => $this->string(50)->notNull(),
            'key' => $this->string(50)->notNull(),
            'value' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('settings_idx', '{{%setting}}', ['category', 'key'], true);
    }

    public function down()
    {
        $this->dropTable('{{%setting}}');
    }
}
