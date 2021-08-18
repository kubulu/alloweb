<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180604_190001_payment_customer extends Migration
{
    public function up()
    {
        $this->createTable('{{%payment_customer}}', [
            'user_id' => $this->primaryKey(),
            'service' => $this->string()->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('payment_customer_user_idx', '{{%payment_customer}}', 'user_id');

        $this->addForeignKey('fk_payment_customer_user',
            '{{%payment_customer}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_payment_customer_user', '{{%payment_customer}}');

        $this->dropTable('{{%payment_customer}}');
    }
}
