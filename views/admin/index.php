<?php

use yii\helpers\Html;

$this->title = 'Admin Panel';

$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="list-group" style="max-width: 520px;">
    <?= Html::a('Articles', ['/article/index'], ['class' => 'list-group-item list-group-item-action']) ?>
    <?= Html::a('Categories', ['/category/index'], ['class' => 'list-group-item list-group-item-action']) ?>
    <?= Html::a('Comments', ['/comment/index'], ['class' => 'list-group-item list-group-item-action']) ?>
    <?= Html::a('Tags', ['/tag/index'], ['class' => 'list-group-item list-group-item-action']) ?>
</div>