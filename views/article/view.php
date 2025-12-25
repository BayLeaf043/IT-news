<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;


/** @var yii\web\View $this */
/** @var app\models\Article $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['/admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Articles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="article-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'content:ntext',
            'category_id',
            [
                'label' => 'Category',
                'value' => $model->category?->title ?? '-',
            ],
            'author_id',
            [
                'label' => 'Author',
                'value' => $model->author?->username ?? '-',
            ],
            [
                'attribute' => 'status',
                'value' => $model->status == 1 ? 'Active' : 'Inactive',
            ],
            [
                'attribute' => 'created_at',
                'value' => Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i'),
            ],
            [
                'attribute' => 'updated_at',
                'value' => $model->updated_at
                    ? Yii::$app->formatter->asDatetime($model->updated_at, 'php:d.m.Y • H:i')
                    : '(not set)',
            ],
            [
                'label' => 'Tags',
                'format' => 'raw',
                'value' => $model->tags
                    ? implode(' ', array_map(fn($t) => '#'.$t->title, $model->tags))
                    : '-',
            ],
            'views',
            [
                'label' => 'Image',
                'format' => 'raw',
                'value' => $model->image
                    ? Html::img(
                        Url::to('@web/' . ltrim($model->image, '/')),
                        ['style' => 'max-width:300px; border-radius:6px;']
                    )
                    : '(no image)',
            ],
        ],
    ]) ?>

</div>
