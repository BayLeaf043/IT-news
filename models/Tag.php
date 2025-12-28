<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $title
 * @property int $created_at
 *
 * @property ArticleTag[] $articleTags
 * @property Article[] $articles
 */
class Tag extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    // таблиця в БД
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    // правила валідації
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 64],
            [['title'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    // підписи атрибутів
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ArticleTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    // звязок з проміжною таблицею article_tag
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::class, ['tag_id' => 'id']);
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з статтями через проміжну таблицю article_tag
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['id' => 'article_id'])->viaTable('article_tag', ['tag_id' => 'id']);
    }

}
