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

    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <div class="page-subtitle">Manage articles: content, categories, tags, images and status.</div>
        </div>

        <?= Html::a('➕ Create Article', ['create'], ['class' => 'btn btn-success btn-rounded']) ?>
    </div>

    <div class="admin-card">
        <div class="admin-card__header">
            <span>Articles list</span>
            <span class="admin-card__hint">Use filters in the header row to search.</span>
        </div>

        <div class="admin-card__body">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => '<div class="grid-summary">{begin}-{end} of {totalCount}</div>',
                    'tableOptions' => ['class' => 'table table-striped table-hover align-middle admin-table'],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'header' => '#',
                            'contentOptions' => ['class' => 'col-serial'],
                        ],

                        [
                            'attribute' => 'id',
                            'contentOptions' => ['class' => 'text-muted'],
                        ],

                        [
                            'attribute' => 'title',
                            'contentOptions' => ['style' => 'max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'],
                        ],

                        [
                            'attribute' => 'content',
                            'value' => function ($model) {
                                return mb_strimwidth(strip_tags($model->content), 0, 60, '...');
                            },
                            'contentOptions' => ['style' => 'max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'],
                        ],

                        [
                            'attribute' => 'category_id',
                            'label' => 'Category',
                            'value' => fn($model) => $model->category?->title,
                            'contentOptions' => ['style' => 'min-width:140px;'],
                        ],

                        [
                            'attribute' => 'author_id',
                            'label' => 'Author',
                            'value' => fn($model) => $model->author?->username,
                            'contentOptions' => ['style' => 'min-width:140px;'],
                        ],

                        [
                            'attribute' => 'image',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if (!$model->image) {
                                    return '<span class="text-muted">—</span>';
                                }
                                return Html::img(Url::to('@web/' . ltrim($model->image, '/')), [
                                    'class' => 'thumb',
                                    'alt' => 'Article image',
                                    'loading' => 'lazy',
                                ]);
                            },
                            'contentOptions' => ['style' => 'width:110px;'],
                            'filter' => false, // фільтр по картинці зазвичай не потрібен
                        ],

                        [
                            'attribute' => 'created_at',
                            'value' => fn($model) => Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i'),
                            'contentOptions' => ['style' => 'min-width:165px;'],
                        ],

                        [
                            'attribute' => 'updated_at',
                            'value' => fn($model) => $model->updated_at
                                ? Yii::$app->formatter->asDatetime($model->updated_at, 'php:d.m.Y • H:i')
                                : '',
                            'contentOptions' => ['style' => 'min-width:165px;'],
                        ],

                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->status == 1
                                    ? '<span class="badge badge-soft-success">Active</span>'
                                    : '<span class="badge badge-soft-muted">Inactive</span>';
                            },
                            // якщо у searchModel є фільтр по status — краще залишити як є
                            'contentOptions' => ['style' => 'width:110px;'],
                        ],

                        [
                            'label' => 'Tags',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if (empty($model->tags)) {
                                    return '<span class="text-muted">—</span>';
                                }
                                return implode(' ', array_map(
                                    fn($t) => '<span class="tag-pill">#' . Html::encode($t->title) . '</span>',
                                    $model->tags
                                ));
                            },
                            'contentOptions' => ['style' => 'min-width:180px;'],
                            'filter' => false, // якщо нема пошуку по тегах — краще прибрати фільтр
                        ],

                        [
                            'attribute' => 'views',
                            'contentOptions' => ['class' => 'text-end', 'style' => 'width:90px;'],
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'header' => 'Actions',
                            'contentOptions' => ['style' => 'white-space:nowrap; width:120px;'],
                            'urlCreator' => function ($action, Article $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            },
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

</div>
