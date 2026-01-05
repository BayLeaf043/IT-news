<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\Article[] $articles */
/** @var \yii\data\Pagination|null $pagination */
/** @var bool $isGuestLimited */
?>

<div class="feed">
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
                        <span class="stat-pill">👀 <?= (int)$article->views ?></span>
                        <span class="stat-pill">💬 <?= $article->getComments()->where(['status' => 1])->count() ?></span>
                    </div>
                </div>

            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php if ($isGuestLimited): ?>
    <div class="guest-more card-soft">
        <div class="guest-more__text">
            <h3>Want more?</h3>
            <p>To read more articles — log in or register.</p>
        </div>
        <div class="guest-more__actions">
            <?= Html::a('Log in', ['/site/login'], ['class' => 'btn btn-primary btn-rounded']) ?>
            <?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-success btn-rounded']) ?>
        </div>
    </div>
<?php elseif ($pagination): ?>
    <div class="pager-wrap">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkContainerOptions' => ['class' => 'page-item'],
            'linkOptions' => ['class' => 'page-link'],
        ]) ?>
    </div>
<?php endif; ?>
