<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180917_180001_queue extends Migration
{
    public function up()
    {
        $this->createTable('{{%queue}}', [
            'id' => $this->primaryKey(),
            'channel' => $this->string()->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->defaultValue(0)->notNull(),
            'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),
            'reserved_at' => $this->integer(),
            'attempt' => $this->integer(),
            'done_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('queue_channel_idx', '{{%queue}}', 'channel');
        $this->createIndex('queue_reserved_at_idx', '{{%queue}}', 'reserved_at');
        $this->createIndex('queue_priority_idx', '{{%queue}}', 'priority');
    }

    public function down()
    {
        $this->dropTable('{{%queue}}');
    }
}
