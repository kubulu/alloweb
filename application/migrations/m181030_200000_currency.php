<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181030_200000_currency extends Migration
{
    public function up()
    {
        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(3)->notNull(),
            'title' => $this->string(64)->notNull(),
            'format' => $this->string(32)->notNull(),
        ]);

        $this->batchInsert('{{%currency}}', ['code', 'title', 'format'], [
            ['USD', 'US Dollar', '$ %s'],
            ['EUR', 'Euro', '€ %s'],
            ['GBP', 'British Pound', '£ %s'],
        ]);

        $this->upsert('{{%setting}}', [
            'category' => 'common',
            'key' => 'paymentStripeEnabled',
            'value' => 1,
        ]);

        $this->upsert('{{%setting}}', [
            'category' => 'common',
            'key' => 'paymentPaypalEnabled',
            'value' => 1,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%currency}}');
    }
}
