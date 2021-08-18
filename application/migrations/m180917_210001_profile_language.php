<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m180917_210001_profile_language extends Migration
{
    public function up()
    {
        $this->addColumn('{{%profile}}', 'language_id', $this->string(5)->null());
    }

    public function down()
    {
        $this->dropColumn('{{%profile}}', 'language_id');
    }
}
