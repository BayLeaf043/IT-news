<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%article_tag}}`.
 */
class m251225_090349_create_article_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%article_tag}}', [
            'id' => $this->primaryKey(),
            'article_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
        ]);

    
        $this->createIndex(
            'uidx-article_tag-article_id-tag_id',
            '{{%article_tag}}',
            ['article_id', 'tag_id'],
            true
        );

        $this->createIndex('idx-article_tag-article_id', '{{%article_tag}}', 'article_id');
        $this->createIndex('idx-article_tag-tag_id', '{{%article_tag}}', 'tag_id');

        $this->addForeignKey(
            'fk-article_tag-article_id',
            '{{%article_tag}}',
            'article_id',
            '{{%article}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-article_tag-tag_id',
            '{{%article_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-article_tag-tag_id', '{{%article_tag}}');
        $this->dropForeignKey('fk-article_tag-article_id', '{{%article_tag}}');

        $this->dropIndex('idx-article_tag-tag_id', '{{%article_tag}}');
        $this->dropIndex('idx-article_tag-article_id', '{{%article_tag}}');
        $this->dropIndex('uidx-article_tag-article_id-tag_id', '{{%article_tag}}');

        $this->dropTable('{{%article_tag}}');
    }
}
