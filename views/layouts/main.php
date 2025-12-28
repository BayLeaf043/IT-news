<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\Category;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

AppAsset::register($this);

// мета-теги та інші налаштування сторінки
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>

<!-- Основний шаблон сторінки -->
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<!-- Верхній навігаційний бар -->
<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    ?>

    <?php
    $form = ActiveForm::begin([
        'action' => ['/site/index'],
        'method' => 'get',
        'options' => ['class' => 'd-flex ms-3', 'style' => 'gap:8px; align-items:center;'],
    ]);?>


    <?= Html::textInput('q', Yii::$app->request->get('q'), [
        'class' => 'form-control form-control-sm',
        'placeholder' => 'Search… (text or #tag)',
        'style' => 'width:240px;',
    ]);?>

    <?= Html::hiddenInput('category_id', Yii::$app->request->get('category_id')); ?>

    <?= Html::submitButton('🔍', ['class' => 'btn btn-sm btn-outline-light']); ?>

    <?php if (Yii::$app->request->get('q')): ?>
        <?= Html::a('✕', ['/site/index', 'category_id' => Yii::$app->request->get('category_id')], [
            'class' => 'btn btn-sm btn-outline-light',
            'title' => 'Clear search',
        ]) ?>
    <?php endif; ?>

    <?php
    ActiveForm::end();

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'], 
        'items' => [
            ['label' => 'Home', 'url' => ['/site/index']],

            !Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin
                ? ['label' => 'Admin Panel', 'url' => ['/admin/index']] : "",


            Yii::$app->user->isGuest
                ? ['label' => 'Login', 'url' => ['/site/login']]
                : [
                    'label' => Yii::$app->user->identity->username,
                    'url' => '#',
                    'items' => [
                        [
                            'label' => 'Logout',
                            'url' => ['/site/logout'],
                            'linkOptions' => ['data-method' => 'post'],
                        ],
                    ],
                ],

            Yii::$app->user->isGuest
                ? ['label' => 'Signup', 'url' => ['/site/signup']]
                : '',
        ],
    ]);
    NavBar::end();
    ?>
</header>


<?= $this->render('//partials/categoryBar') ?>

<!-- Основний вміст сторінки -->
<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<!-- Нижній колонтитул сторінки -->
<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; My Company <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end"><?= Yii::powered() ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
