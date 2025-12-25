<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\models\User;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Article $model */
/** @var yii\widgets\ActiveForm $form */
$categories = ArrayHelper::map(Category::find()->orderBy('title')->all(), 'id', 'title');
?>

<div class="article-form">

    <?php $form = ActiveForm::begin([

        'options' => ['enctype' => 'multipart/form-data']

    ]); ?>

    <div class="mb-3">

        <label class="form-label">Author</label>

        <div class="form-control" readonly>

            <?= !Yii::$app->user->isGuest ? Yii::$app->user->identity->username : '-' ?>

        </div>

    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'category_id')->dropDownList($categories, ['prompt' => '— Select category —']) ?>

    <?= $form->field($model, 'status')->radioList(
        [1 => 'Active', 0 => 'Inactive'],
        ['itemOptions' => ['class' => 'form-check-input'], 'class' => 'd-flex gap-3']
    ) ?>

    <?= $form->field($model, 'tags_input')
        ->textInput(['placeholder' => '#ai #google або AI, Google, IT'])
        ->hint('You can enter tags via # or comma') ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <?php if ($model->image): ?>
        <?= Html::img(Url::to('@web/' . ltrim($model->image, '/')), ['style' => 'max-width:200px;']) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
