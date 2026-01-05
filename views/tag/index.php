<?php

use app\models\Tag;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TagSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tags';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['/admin/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="tag-index">

    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <div class="page-subtitle">Manage tags for articles.</div>
        </div>

        <?= Html::a('➕ Create Tag', ['create'], ['class' => 'btn btn-success btn-rounded']) ?>
    </div>

    <div class="admin-card">
        <div class="admin-card__header">
            <span>Tags list</span>
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
                            'contentOptions' => ['style' => 'min-width:220px;'],
                        ],

                        [
                            'attribute' => 'created_at',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i');
                            },
                            'contentOptions' => ['style' => 'min-width:180px;'],
                        ],

                        [
                            'class' => ActionColumn::className(),
                            'header' => 'Actions',
                            'contentOptions' => ['style' => 'white-space:nowrap; width:120px;'],
                            'urlCreator' => function ($action, Tag $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

</div>