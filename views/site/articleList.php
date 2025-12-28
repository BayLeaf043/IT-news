<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\Article[] $articles */
/** @var \yii\data\Pagination|null $pagination */
/** @var bool $isGuestLimited */
?>

<!-- Список статей -->
<?php foreach ($articles as $article): ?>
    <a class="article-card-link" href="<?= Url::to(['site/view', 'id' => $article->id]) ?>">
        <div class="article-card">
            
            <?php if ($article->category): ?>
                <div class="article-topic"><?= Html::encode($article->category->title) ?></div>
            <?php endif; ?>

            <h2 class="article-title"><?= Html::encode($article->title) ?></h2>

            <div class="article-preview">
                <?= Html::encode(mb_strimwidth(strip_tags($article->content), 0, 200, '...')) ?>
            </div>

            <div class="article-meta">
                <span class="article-date">
                    Published: <?= Yii::$app->formatter->asDatetime($article->created_at, 'php:d.m.Y H:i') ?>
                </span>

                <div class="article-stats">
                    <span class="article-views">
                        👀 <?= (int)$article->views ?>
                    </span>
                    <span class="article-comments">
                        💬 <?= $article->getComments()->where(['status' => 1])->count() ?>
                    </span>
                </div>
            </div>
        </div>
        </a>
<?php endforeach; ?>

<!-- Повідомлення для гостей та пагінація -->
<?php if ($isGuestLimited): ?>
    <div class="guest-more">
        <p>To read more articles — log in or register.</p>
        <?= Html::a('Log in', ['/site/login'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-success']) ?>
    </div>
<?php elseif ($pagination): ?>
    <?= \yii\widgets\LinkPager::widget(['pagination' => $pagination,
    'linkContainerOptions' => ['class' => 'page-item'],
    'linkOptions' => ['class' => 'page-link'],]) ?>
<?php endif; ?>
