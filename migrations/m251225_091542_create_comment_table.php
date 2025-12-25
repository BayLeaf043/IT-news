<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m251225_091542_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(), 
            'text' => $this->text()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0), 
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-comment-article_id', '{{%comment}}', 'article_id');
        $this->createIndex('idx-comment-user_id', '{{%comment}}', 'user_id');
        $this->createIndex('idx-comment-parent_id', '{{%comment}}', 'parent_id');

        $this->addForeignKey(
            'fk-comment-article_id',
            '{{%comment}}',
            'article_id',
            '{{%article}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-comment-user_id',
            '{{%comment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-comment-parent_id',
            '{{%comment}}',
            'parent_id',
            '{{%comment}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-comment-parent_id', '{{%comment}}');
        $this->dropForeignKey('fk-comment-user_id', '{{%comment}}');
        $this->dropForeignKey('fk-comment-article_id', '{{%comment}}');

        $this->dropIndex('idx-comment-parent_id', '{{%comment}}');
        $this->dropIndex('idx-comment-user_id', '{{%comment}}');
        $this->dropIndex('idx-comment-article_id', '{{%comment}}');

        $this->dropTable('{{%comment}}');
    }
}
