<?php

use app\models\Comment;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CommentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Comments';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['/admin/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="comment-index">

    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <div class="page-subtitle">Moderate and manage comments for articles.</div>
        </div>
        <!-- Тут зазвичай нема Create, бо коментарі створюють користувачі -->
    </div>

    <div class="admin-card">
        <div class="admin-card__header">
            <span>Comments list</span>
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
                            'attribute' => 'article_id',
                            'label' => 'Article',
                            'value' => fn($model) => $model->article?->title,
                            'contentOptions' => [
                                'style' => 'min-width:200px; max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'
                            ],
                        ],

                        [
                            'attribute' => 'user_id',
                            'label' => 'Author',
                            'value' => fn($model) => $model->user?->username ?? '—',
                            'contentOptions' => ['style' => 'min-width:140px;'],
                        ],

                        [
                            'attribute' => 'parent_id',
                            'label' => 'Parent Comment',
                            'value' => function ($model) {
                                if (!$model->parent) return '—';
                                return mb_strimwidth(strip_tags($model->parent->text), 0, 40, '...');
                            },
                            'contentOptions' => [
                                'style' => 'min-width:180px; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'
                            ],
                        ],

                        [
                            'attribute' => 'text',
                            'label' => 'Text',
                            'value' => function ($model) {
                                return mb_strimwidth(strip_tags($model->text), 0, 80, '...');
                            },
                            'contentOptions' => [
                                'style' => 'min-width:240px; max-width:360px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;'
                            ],
                        ],

                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->status == 1
                                    ? '<span class="badge badge-soft-success">Active</span>'
                                    : '<span class="badge badge-soft-muted">Inactive</span>';
                            },
                            'contentOptions' => ['style' => 'width:110px;'],
                        ],

                        [
                            'attribute' => 'created_at',
                            'value' => fn($model) => Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i'),
                            'contentOptions' => ['style' => 'min-width:180px;'],
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'header' => 'Actions',
                            'contentOptions' => ['style' => 'white-space:nowrap; width:120px;'],
                            'urlCreator' => function ($action, Comment $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

</div>
