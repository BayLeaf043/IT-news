<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

use yii\helpers\StringHelper;


/** @var yii\web\View $this */
/** @var array $articles */
/** @var yii\data\Pagination|null $pagination */
/** @var bool $isGuestLimited */
/** @var string $pageTitle */
/** @var string $q */

$this->title = $pageTitle;
$this->registerCssFile('@web/css/article.css');
?>

<h1><?= Html::encode($pageTitle ?? $this->title) ?></h1>

<?= $this->render('articleList', [
    'articles' => $articles,
    'pagination' => $pagination,
    'isGuestLimited' => $isGuestLimited,
]) ?>

<?php if (empty($articles)): ?>
    <div class="alert alert-info mt-3">
        Nothing found<?= $q ? ' for: <b>' . Html::encode($q) . '</b>' : '' ?>.
    </div>
<?php endif; ?>