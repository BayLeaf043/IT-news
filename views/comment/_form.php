<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Comment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="comment-form">

<?php $form = ActiveForm::begin(); ?>

    <div class="mb-3">

        <label class="form-label">Comment: </label>

        <div class="form-control" readonly>

            <?= $model->text ?>

        </div>

    </div>

    <?= $form->field($model, 'status')->radioList(
        [1 => 'Active', 0 => 'Inactive'],
        ['itemOptions' => ['class' => 'form-check-input'], 'class' => 'd-flex gap-3']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
