<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180913_180001_verification extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profile}}', 'is_verified', $this->boolean()->defaultValue(false));
        $this->createIndex('profile_verified_idx', '{{%profile}}', 'is_verified');

        $this->createTable('{{%verification}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'verification_photo' => $this->string(500),
            'is_viewed' => $this->boolean()->defaultValue(false),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('verification_user_idx', '{{%verification}}', 'user_id');
        $this->createIndex('verification_viewed_idx', '{{%verification}}', 'is_viewed');

        $this->addForeignKey('fk_verification_user',
            '{{%verification}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropColumn('{{%profile}}', 'is_verified');

        $this->dropForeignKey('fk_verification_user', '{{%verification}}');

        $this->dropTable('{{%verification}}');
    }
}
