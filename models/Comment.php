<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $article_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $text
 * @property int $status
 * @property int $created_at
 *
 * @property Article $article
 * @property Comment[] $comments
 * @property Comment $parent
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    // таблиця в БД
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    // правила валідації
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 0],
            [['article_id', 'user_id', 'parent_id', 'status'], 'integer'],
            [['text'], 'string'],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comment::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'article_id' => 'Article ID',
            'user_id' => 'User ID',
            'parent_id' => 'Parent ID',
            'text' => 'Text',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Article]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з статтею
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з відповідями на коментар
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з батьківським коментарем
    public function getParent()
    {
        return $this->hasOne(Comment::class, ['id' => 'parent_id']);
    }

    public function getReplies()
    {
        return $this->hasMany(Comment::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з користувачем
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
