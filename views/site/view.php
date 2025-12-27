<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Article $model */
/** @var int $commentsCount */
/** @var $model app\models\Article */
/** @var \app\models\Comment $commentForm */
/** @var \app\models\Comment[] $comments */

$this->title = $model->title;
$this->registerCssFile('@web/css/article.css');

$shareUrl   = Url::to(['site/view', 'id' => $model->id], true);
$shareTitle = $model->title;

// короткий опис
$shareText = mb_strimwidth(strip_tags($model->content), 0, 140, '...');

$shareLinks = [
    'Facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl),
    'X'        => 'https://twitter.com/intent/tweet?url=' . urlencode($shareUrl) . '&text=' . urlencode($shareTitle),
    'Telegram' => 'https://t.me/share/url?url=' . urlencode($shareUrl) . '&text=' . urlencode($shareTitle),
    'LinkedIn' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareUrl),
];

$imgUrl = null;
if (!empty($model->image)) {
    $imgUrl = Url::to('@web/' . ltrim($model->image, '/'), true);
}

// OpenGraph meta tags
$this->registerMetaTag(['property' => 'og:type', 'content' => 'article']);
$this->registerMetaTag(['property' => 'og:url', 'content' => $shareUrl]);
$this->registerMetaTag(['property' => 'og:title', 'content' => $shareTitle]);
$this->registerMetaTag(['property' => 'og:description', 'content' => $shareText]);

if ($imgUrl) {
    $this->registerMetaTag(['property' => 'og:image', 'content' => $imgUrl]);
    $this->registerMetaTag(['property' => 'og:image:alt', 'content' => $shareTitle]);
}

// Twitter card
$this->registerMetaTag(['name' => 'twitter:card', 'content' => $imgUrl ? 'summary_large_image' : 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $shareTitle]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $shareText]);
if ($imgUrl) {
    $this->registerMetaTag(['name' => 'twitter:image', 'content' => $imgUrl]);
}
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
                <a class="tag-pill" href="<?= \yii\helpers\Url::to(['site/tag', 'tag' => $tag->title]) ?>">
                    #<?= \yii\helpers\Html::encode($tag->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <div class="share-box">
        <div class="share-title">Share:</div>

            <div class="share-actions">
                <?php foreach ($shareLinks as $label => $link): ?>
                <?= Html::a($label, $link, [
                    'class' => 'share-btn share-' . strtolower(preg_replace('/[^a-z]/i', '', $label)),
                    'target' => '_blank',
                    'rel' => 'noopener',
                ]) ?>
            <?php endforeach; ?>

            <button type="button"
                class="share-btn share-copy"
                onclick="navigator.clipboard.writeText('<?= Html::encode($shareUrl) ?>')
                    .then(() => alert('Link copied!'));">
                Copy link
            </button>
        </div>
    </div>


    <div id="discussion" class="discussion">
        <h3 class="discussion-title">Discussion (<?= (int)$commentsCount ?>)</h3>

        <?php if (Yii::$app->user->isGuest): ?>
            <div class="discussion-guest">
                <p>To join the discussion — log in or register.</p>
                <?= Html::a('Log in', ['/site/login'], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Sign up', ['/site/signup'], ['class' => 'btn btn-success']) ?>
            </div>
        <?php else: ?>
            <div class="comment-form">
                <div class="avatar"><?= Html::encode(mb_strtoupper(mb_substr(Yii::$app->user->identity->username, 0, 1))) ?></div>
                <div class="comment-form-body">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['site/comment-create', 'id' => $model->id]),
                        'method' => 'post',
                        'options' => ['class' => 'comment-form-inner'],
                    ]); ?>

                    <?= Html::hiddenInput('parent_id', '', ['id' => 'parent_id']) ?>

                    <?= $form->field($commentForm, 'text')
                        ->textarea(['rows' => 3, 'placeholder' => 'Join the discussion...'])
                        ->label(false) ?>

                    <div class="comment-form-actions">
                        <span class="reply-to" id="replyToLabel" style="display:none;"></span>
                        <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                        <button type="button" class="btn btn-link" id="cancelReply" style="display:none;">Cancel reply</button>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        <?php endif; ?>


        <?php
        // будуємо дерево: parent_id => [comments]
        $byParent = [];
        foreach ($comments as $c) {
            $pid = $c->parent_id ?: 0;
            $byParent[$pid][] = $c;
        }

        $renderTree = function($parentId, $level) use (&$renderTree, $byParent) {
            if (empty($byParent[$parentId])) return;

            echo '<div class="comment-list level-'.$level.'">';
            foreach ($byParent[$parentId] as $c) {
                $initial = $c->user ? mb_strtoupper(mb_substr($c->user->username, 0, 1)) : '?';
                $name = $c->user ? $c->user->username : 'User';

                echo '<div class="comment">';
                echo '  <div class="avatar">'.Html::encode($initial).'</div>';
                echo '  <div class="comment-body">';
                echo '      <div class="comment-head">';
                echo '          <span class="comment-user">'.Html::encode($name).'</span>';
                echo '          <span class="comment-date">'.Yii::$app->formatter->asDatetime($c->created_at, 'php:d.m.Y H:i').'</span>';
                echo '      </div>';
                echo '      <div class="comment-text">'.nl2br(Html::encode($c->text)).'</div>';

                if (!Yii::$app->user->isGuest) {
                    echo '  <button class="reply-btn" type="button" data-parent="'.$c->id.'" data-user="'.Html::encode($name).'">Reply</button>';
                }

                echo '  </div>';
                echo '</div>';

                // діти
                $renderTree($c->id, $level + 1);
            }
            echo '</div>';
        };

        $renderTree(0, 0);
        ?>
    </div> <!-- /discussion -->

</div>


<?php
$js = <<<JS
document.addEventListener('click', function(e){
    const btn = e.target.closest('.reply-btn');
    if (!btn) return;

    const parentId = btn.getAttribute('data-parent');
    const user = btn.getAttribute('data-user');

    const parentInput = document.getElementById('parent_id');
    const label = document.getElementById('replyToLabel');
    const cancel = document.getElementById('cancelReply');

    parentInput.value = parentId;
    label.style.display = '';
    cancel.style.display = '';
    label.textContent = 'Replying to: ' + user;

    // скрол до форми
    document.getElementById('discussion').scrollIntoView({behavior:'smooth', block:'start'});
});

document.getElementById('cancelReply')?.addEventListener('click', function(){
    document.getElementById('parent_id').value = '';
    document.getElementById('replyToLabel').style.display = 'none';
    this.style.display = 'none';
});
JS;

$this->registerJs($js);
?>