<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $category_id
 * @property int $author_id
 * @property int $status
 * @property int $created_at
 * @property int|null $updated_at
 * @property int $views
 * @property string|null $image
 *
 * @property ArticleTag[] $articleTags
 * @property User $author
 * @property Category $category
 * @property Comment[] $comments
 * @property Tag[] $tags
 */
class Article extends \yii\db\ActiveRecord
{
    /** @var UploadedFile|null */
    public $imageFile;

    /** @var string */
    public string $tags_input = '';


    /**
     * {@inheritdoc}
     */
    // таблиця в БД
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    // правила валідації
    public function rules()
    {
        return [
            [['updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['views'], 'default', 'value' => 0],
            [['title', 'content', 'category_id', 'author_id'], 'required'],
            [['content'], 'string'],
            [['category_id', 'author_id', 'status', 'created_at', 'updated_at', 'views'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'checkExtensionByMimeType' => false,'maxSize' => 2*1024*1024],
            [['image'], 'string', 'max' => 255],
            [['tags_input'], 'safe'],
            [['tags_input'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    // підписи для полів
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'author_id' => 'Author ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'views' => 'Views',
            'image' => 'Image',
            'tags_input' => 'Hashtags',
        ];
    }

    /**
     * Gets query for [[ArticleTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з проміжною таблицею article_tag
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::class, ['article_id' => 'id']);
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з автором статті
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з категорією статті
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з коментарями статті
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['article_id' => 'id']);
    }

    /**
     * Gets query for [[Tags]].
     *
     * @return \yii\db\ActiveQuery
     */
    // зв'язок з тегами статті через проміжну таблицю article_tag
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable('article_tag', ['article_id' => 'id']);
    }

    // після завантаження моделі зі сторінки редагування
    public function afterFind()
    {
        parent::afterFind();
        // підставляємо поточні теги в поле для редагування
        $this->tags_input = implode(' ', array_map(fn($t) => '#'.$t->title, $this->tags));
    }

    // після видалення статті
    public function afterDelete()
    {
        parent::afterDelete();

        if ($this->image) {
            $path = Yii::getAlias('@webroot/' . ltrim($this->image, '/'));
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }


    // розбір рядка з тегами/хештегами
    public static function parseTags(string $input): array
    {
        $input = trim($input);
        if ($input === '') return [];

        // якщо є # — дістаємо все після # до пробілу/коми
        if (strpos($input, '#') !== false) {
            preg_match_all('/#([a-zA-Z0-9_а-яА-ЯіїєІЇЄ-]+)/u', $input, $m);
            $tags = $m[1] ?? [];
        } else {
            // інакше — розділяємо комами
            $tags = preg_split('/\s*,\s*/u', $input, -1, PREG_SPLIT_NO_EMPTY);
        }

        // чистимо, унікалізуємо, приводимо до нормального вигляду
        $tags = array_map(fn($t) => mb_strtolower(trim($t)), $tags);
        $tags = array_filter($tags, fn($t) => $t !== '');
        $tags = array_values(array_unique($tags));

        return $tags;
    }

    // збереження тегів зі сторінки редагування
    public function saveTagsFromInput(): void
    {
        $tagNames = self::parseTags($this->tags_input);

        // очистити старі зв'язки
        \app\models\ArticleTag::deleteAll(['article_id' => $this->id]);

        foreach ($tagNames as $name) {
            $tag = \app\models\Tag::findOne(['title' => $name]);
            if (!$tag) {
                $tag = new \app\models\Tag();
                $tag->title = $name;
                $tag->created_at = time();
                $tag->save(false);
            }

            $link = new \app\models\ArticleTag();
            $link->article_id = $this->id;
            $link->tag_id = $tag->id;
            $link->save(false);
        }
    }

    // перед збереженням моделі
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $time = time();

        if ($insert) {
            // при створенні
            $this->created_at = $time;
        }

        // при кожному оновленні
        $this->updated_at = $time;

        return true;
    }

}
