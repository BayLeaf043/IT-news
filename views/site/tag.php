<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \app\models\Tag $tagModel */
/** @var \app\models\Article[] $articles */
/** @var \yii\data\Pagination $pagination */
/** @var string $pageTitle */

$this->title = $pageTitle;
$this->registerCssFile('@web/css/article.css');
?>

<h1><?= Html::encode($pageTitle) ?></h1>

<?= $this->render('articleList', [
    'articles' => $articles,
    'pagination' => $pagination,
    'isGuestLimited' => Yii::$app->user->isGuest,
]) ?>