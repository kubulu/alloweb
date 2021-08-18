<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180601_183001_profile extends Migration
{
    public function up()
    {
        $this->createTable('{{%profile}}', [
            'user_id' => $this->integer()->notNull()->append('PRIMARY KEY'),
            'photo_id' => $this->integer()->null(),
            'name' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'sex' => $this->integer()->null(),
            'status' => $this->integer()->null(),
            'dob' => $this->date()->null(),
            'looking_for_sex' => $this->integer()->null(),
            'looking_for_from_age' => $this->integer()->null(),
            'looking_for_to_age' => $this->integer()->null(),
            'timezone' => $this->string(40)->null(),
            'country' => $this->string(2)->null(),
            'city' => $this->integer()->unsigned()->null(),
            'latitude' => $this->decimal(10, 8)->null(),
            'longitude' => $this->decimal(11, 8)->null(),
        ], $this->tableOptions);

        $this->addForeignKey('fk_profile_photo', '{{%profile}}',
            'photo_id', '{{%photo}}',
            'id', 'set null', $this->cascade
        );

        $this->addForeignKey('{{%fk_user_profile}}',
            '{{%profile}}', 'user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
    }
}
