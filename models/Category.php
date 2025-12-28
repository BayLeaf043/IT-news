<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 * @property int $created_at
 *
 * @property Article[] $articles
 */
class Category extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    // таблиця в БД
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    // правила валідації
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
        ];
    }

    // автоматичне заповнення created_at
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false, 
            ],
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
     * Gets query for [[Articles]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з статтями
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['category_id' => 'id']);
    }

}
