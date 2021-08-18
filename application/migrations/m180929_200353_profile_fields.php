<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180929_200353_profile_fields extends Migration
{
    public function up()
    {
        $this->createTable('{{%profile_field_category}}', [
            'id' => $this->primaryKey(),
            'alias' => $this->string(255)->notNull(),
            'title' => $this->string(255)->notNull(),
            'language_category' => $this->string(64),
            'sort_order' => $this->integer()->unsigned()->defaultValue(100),
            'is_visible' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('profile_field_category_alias_idx', '{{%profile_field_category}}', ['alias'], true);
        $this->createIndex('profile_field_category_visible_idx', '{{%profile_field_category}}', 'is_visible');

        $this->createTable('{{%profile_field}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'field_class' => $this->string(255)->notNull(),
            'field_config' => $this->text(),
            'alias' => $this->string(255)->notNull(),
            'title' => $this->string(255)->notNull(),
            'language_category' => $this->string(64),
            'sort_order' => $this->integer()->unsigned()->defaultValue(100),
            'is_visible' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('profile_field_alias_idx', '{{%profile_field}}', ['category_id', 'alias'], true);
        $this->createIndex('profile_field_visible_idx', '{{%profile_field}}', 'is_visible');
        $this->createIndex('profile_field_category_idx', '{{%profile_field}}', 'category_id');

        $this->addForeignKey('fk_profile_field_category',
            '{{%profile_field}}', 'category_id',
            '{{%profile_field_category}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->createTable('{{%profile_extra}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'field_id' => $this->integer()->notNull(),
            'value' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $this->tableOptions);

        $this->createIndex('profile_extra_user_idx', '{{%profile_extra}}', 'user_id');
        $this->createIndex('profile_extra_type_idx', '{{%profile_extra}}', 'field_id');
        $this->createIndex('profile_extra_user_field_idx', '{{%profile_extra}}', ['user_id', 'field_id'], true);

        $this->addForeignKey('fk_profile_extra_user',
            '{{%profile_extra}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_profile_extra_field',
            '{{%profile_extra}}', 'field_id',
            '{{%profile_field}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_profile_extra_field', '{{%profile_extra}}');
        $this->dropForeignKey('fk_profile_extra_user', '{{%profile_extra}}');
        $this->dropForeignKey('fk_profile_field_category', '{{%profile_field}}');
        $this->dropTable('{{%profile_extra}}');
        $this->dropTable('{{%profile_field}}');
        $this->dropTable('{{%profile_field_category}}');
    }
}
