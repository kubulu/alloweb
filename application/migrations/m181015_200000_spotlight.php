<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181015_200000_spotlight extends Migration
{
    public function up()
    {
        $this->createTable('{{%spotlight}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'photo_id' => $this->integer()->notNull(),
            'message' => $this->string(255)->null(),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('spotlight_user_idx', '{{%spotlight}}', 'user_id');
        $this->createIndex('spotlight_photo_idx', '{{%spotlight}}', 'photo_id');

        $this->addForeignKey('fk_spotlight_user',
            '{{%spotlight}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_spotlight_photo',
            '{{%spotlight}}', 'photo_id',
            '{{%photo}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->upsert('{{%setting}}', [
            'category' => 'common',
            'key' => 'priceSpotlight',
            'value' => '50',
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('fk_spotlight_photo', '{{%spotlight}}');
        $this->dropForeignKey('fk_spotlight_user', '{{%spotlight}}');
        $this->dropTable('{{%spotlight}}');
    }
}
