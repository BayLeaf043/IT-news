<?php
use app\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;

$categories = Category::find()
    ->orderBy(['title' => SORT_ASC])
    ->all();

$currentCategoryId = Yii::$app->request->get('category_id'); 
$q = Yii::$app->request->get('q');
?>

<!-- Панель категорій для фільтрації статей -->
<div class="category-bar">
    <div class="category-bar-inner">
        <?= Html::a('All', ['/site/index', 'q' => $q], [
            'class' => 'category-pill ' . (empty($currentCategoryId) ? 'active' : ''),
        ]) ?>

        <?php foreach ($categories as $cat): ?>
            <?= Html::a(Html::encode($cat->title), ['/site/index', 'category_id' => $cat->id, 'q' => $q], [
                'class' => 'category-pill ' . ((string)$currentCategoryId === (string)$cat->id ? 'active' : ''),
            ]) ?>
        <?php endforeach; ?>
    </div>
</div>