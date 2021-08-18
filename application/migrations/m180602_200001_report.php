<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180602_200001_report extends Migration
{
    public function up()
    {
        $this->createTable('{{%report}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'reported_user_id' => $this->integer()->notNull(),
            'is_viewed' => $this->boolean()->defaultValue(false),
            'reason' => $this->string(32)->notNull(),
            'description' => $this->string(200)->null(),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('report_from_user_idx', '{{%report}}', 'from_user_id');
        $this->createIndex('report_to_user_idx', '{{%report}}', 'reported_user_id');
        $this->createIndex('report_reason_idx', '{{%report}}', 'reason');
        $this->createIndex('report_viewed_idx', '{{%report}}', 'is_viewed');
        $this->createIndex('report_unique_idx', '{{%report}}', [
            'from_user_id', 'reported_user_id', 'reason'
        ], true);

        $this->addForeignKey('fk_report_from_user',
            '{{%report}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_report_reported_user',
            '{{%report}}', 'reported_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_report_from_user', '{{%report}}');
        $this->dropForeignKey('fk_report_reported_user', '{{%report}}');

        $this->dropTable('{{%report}}');
    }
}
