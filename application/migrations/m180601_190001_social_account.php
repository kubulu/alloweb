<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_190001_social_account extends Migration
{
    public function up()
    {
        $this->createTable('{{%social_account}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'provider' => $this->string()->notNull(),
            'client_id' => $this->string()->notNull(),
            'data' => $this->text()->null(),
            'code' => $this->string(32)->null(),
            'created_at' => $this->integer()->null(),
            'email' => $this->string()->null(),
            'username' =>$this->string()->null(),
        ], $this->tableOptions);

        $this->createIndex('{{%account_unique_code}}', '{{%social_account}}', 'code', true);

        $this->createIndex('{{%account_unique}}', '{{%social_account}}', ['provider', 'client_id'], true);
        $this->addForeignKey('{{%fk_user_account}}',
            '{{%social_account}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropTable('{{%social_account}}');
    }
}
