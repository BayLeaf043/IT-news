<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Article $model */
/** @var int $commentsCount */

$this->title = $model->title;
$this->registerCssFile('@web/css/article.css');
?>

<div class="article-page">

    <div class="article-top">
        <?php if ($model->category): ?>
            <span class="article-topic"><?= Html::encode($model->category->title) ?></span>
        <?php endif; ?>

        <div class="article-meta">
            <span class="article-date">
                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y H:i') ?>
            </span>

            <div class="article-stats">
                <span class="article-views">👀 <?= (int)$model->views ?></span>
                <span class="article-comments">💬 <?= (int)$commentsCount ?></span>

                <?php if ($model->author): ?>
                    <span class="article-author">👤 <?= Html::encode($model->author->username) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h1 class="article-h1"><?= Html::encode($model->title) ?></h1>

    <?php if (!empty($model->image)): ?>
        <div class="article-image">
            <?= Html::img('@web/' . ltrim($model->image, '/'), ['alt' => $model->title]) ?>
        </div>
    <?php endif; ?>

    <div class="article-content">
        <?= nl2br(Html::encode($model->content)) ?>
    </div>

    <?php if (!empty($model->tags)): ?>
        <div class="article-tags">
            <?php foreach ($model->tags as $tag): ?>
                <span class="tag">#<?= Html::encode($tag->title) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>