<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181010_200000_encounters extends Migration
{
    public function up()
    {
        $this->createTable('{{%encounter}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'is_liked' => $this->boolean(),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('encounter_from_user_idx', '{{%encounter}}', 'from_user_id');
        $this->createIndex('encounter_to_user_idx', '{{%encounter}}', 'to_user_id');
        $this->createIndex('encounter_from_to_user_idx', '{{%encounter}}', ['from_user_id', 'to_user_id'], true);

        $this->addForeignKey('fk_encounter_from_user',
            '{{%encounter}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_encounter_to_user',
            '{{%encounter}}', 'to_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_encounter_from_user', '{{%encounter}}');
        $this->dropForeignKey('fk_encounter_to_user', '{{%encounter}}');
        $this->dropTable('{{%encounter}}');
    }
}
