<?php

use hauntd\core\migrations\Migration;
use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181114_130001_sex extends Migration
{
    public function up()
    {
        $this->createTable('{{%sex}}', [
            'id' => $this->primaryKey(),
            'sex' => $this->integer(),
            'alias' => $this->string(64)->notNull(),
            'title' => $this->string(255)->notNull(),
            'title_plural' => $this->string(255),
            'icon' => $this->string(255),
        ]);

        $this->createIndex('sex_idx', '{{%sex}}', 'sex');
        $this->createIndex('sex_alias_idx', '{{%sex}}', 'alias', true);

        $this->upsert('{{%sex}}', [
            'id' => 1,
            'sex' => Profile::SEX_MALE,
            'alias' => 'male',
            'title' => Yii::t('app', 'Man'),
            'title_plural' => Yii::t('app', 'Men'),
            'icon' => 'fa fa-male'
        ]);

        $this->upsert('{{%sex}}', [
            'id' => 2,
            'sex' => Profile::SEX_FEMALE,
            'alias' => 'female',
            'title' => Yii::t('app', 'Woman'),
            'title_plural' => Yii::t('app', 'Women'),
            'icon' => 'fa fa-female'
        ]);

        $this->update('{{%profile}}', ['sex' => null], ['sex' => Profile::SEX_NOT_SET]);
    }

    public function down()
    {
        $this->dropTable('{{%sex}}');
    }
}
