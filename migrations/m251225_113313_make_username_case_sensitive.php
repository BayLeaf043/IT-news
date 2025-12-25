<?php

use yii\db\Migration;

class m251225_113313_make_username_case_sensitive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user}}', 'username', $this->string(50)->notNull()
        ->append('COLLATE utf8mb4_bin'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%user}}', 'username', $this->string(50)->notNull()
        ->append('COLLATE utf8mb4_general_ci'));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251225_113313_make_username_case_sensitive cannot be reverted.\n";

        return false;
    }
    */
}
