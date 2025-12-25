<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%article}}`.
 */
class m251225_085933_create_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1), 
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-article-category_id', '{{%article}}', 'category_id');
        $this->addForeignKey(
            'fk-article-category_id',
            '{{%article}}',
            'category_id',
            '{{%category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createIndex('idx-article-author_id', '{{%article}}', 'author_id');
        $this->addForeignKey(
            'fk-article-author_id',
            '{{%article}}',
            'author_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-article-author_id', '{{%article}}');
        $this->dropIndex('idx-article-author_id', '{{%article}}');

        $this->dropForeignKey('fk-article-category_id', '{{%article}}');
        $this->dropIndex('idx-article-category_id', '{{%article}}');

        $this->dropTable('{{%article}}');
    }
}
