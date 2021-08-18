<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180604_180001_balance extends Migration
{
    public function up()
    {
        $this->createTable('{{%balance}}', [
            'user_id' => $this->primaryKey(),
            'balance' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('balance_user_idx', '{{%balance}}', 'user_id');

        $this->createTable('{{%balance_transaction}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->integer(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('balance_transaction_user_idx', '{{%balance_transaction}}', 'user_id');

        $this->addForeignKey('fk_balance_user',
            '{{%balance}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_balance_transaction_user',
            '{{%balance_transaction}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_balance_user', '{{%balance}}');
        $this->dropForeignKey('fk_balance_transaction_user', '{{%balance_transaction}}');

        $this->dropTable('{{%balance}}');
        $this->dropTable('{{%balance_transaction}}');
    }
}
