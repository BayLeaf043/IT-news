<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m251225_084521_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
        'id' => $this->primaryKey(),
        'username' => $this->string(50)->notNull()->unique(),
        'email' => $this->string(255)->notNull()->unique(),
        'password_hash' => $this->string(255)->notNull(),
        'auth_key' => $this->string(32)->notNull(),
        'is_admin' => $this->boolean()->notNull()->defaultValue(0),
        'created_at' => $this->integer()->notNull(),
    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
