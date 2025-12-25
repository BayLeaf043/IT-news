<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Comment $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['/admin/index']];
$this->params['breadcrumbs'][] = ['label' => 'Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="comment-view">

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
            'article_id',
            [
                'attribute' => 'article_id',
                'label' => 'Article',
                'value' => function ($model) {
                    return $model->article?->title;
                },
            ],
            'user_id',
            [
                'attribute' => 'user_id',
                'label' => 'Author',
                'value' => function ($model) {
                    return $model->user?->username ?? '-';
                },
            ],
            'parent_id',
            [
                'attribute' => 'parent_id',
                'label' => 'Parent Comment',
                'value' => function ($model) {
                    return $model->parent ? $model->parent->text : '-';
                },
            ],
            'text:ntext',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status == 1 ? 'Active' : 'Inactive';
                },
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y • H:i');
                },
            ],
        ],
    ]) ?>

</div>
