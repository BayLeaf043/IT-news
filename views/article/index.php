<?php

use app\models\Article;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ArticleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Articles';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['/admin/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Article', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            [
                'attribute' => 'content',
                'value' => function ($model) {
                    return mb_strimwidth(strip_tags($model->content), 0, 60, '...');
                },
            ],
            [
                'attribute' => 'category_id',
                'label' => 'Category',
                'value' => function ($model) {
                   return $model->category?->title;
                },
            ],
            [
                'attribute' => 'author_id',
                'label' => 'Author',
                'value' => function ($model) {
                    return $model->author?->username;
                },
            ],
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    if (!$model->image) return null;
                    // якщо зберігаєш шлях типу "uploads/xxx.jpg"
                        return Html::img(Url::to('@web/' . ltrim($model->image, '/')), [
                            'style' => 'max-width:80px; max-height:60px; object-fit:cover;'
                        ]);
                    },
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i');
                },
            ],
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return $model->updated_at
                    ? Yii::$app->formatter->asDatetime($model->updated_at, 'php:d.m.Y • H:i') : '';
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status == 1 ? 'Active' : 'Inactive';
                },
            ],
            [
                'label' => 'Tags',
                'format' => 'raw',
                'value' => function ($model) {
                    if (empty($model->tags)) {
                        return '-';
                    }
                    return implode(' ', array_map(fn($t) => '#' . $t->title, $model->tags));
                },
            ],
            'views',
        
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Article $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
