<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Реєстрація';

?>

<style>
    .form-wrapper {
        max-width: 430px;
        margin: 40px auto;
        background: #f7faff;             
        padding: 25px 30px;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        border: 1px solid #e0e8f5;
    }

    .form-wrapper h2 {
        text-align: center;
        font-weight: 600;
        margin-bottom: 20px;
        color: #3b4b71;
    }

    .has-error .form-control {
        border-color: #e74c3c !important;
        background: #fff6f6;
        box-shadow: none;
    }

    has-error .help-block {
        color: #e74c3c;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-wrapper .btn-primary,
    .form-wrapper .btn-success {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 8px;
    }
</style>

    <div class="form-wrapper">
    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin([
        'id' => 'signup-form',
    ]); ?>

        <?= $form->field($model, 'username')
            ->textInput(['placeholder' => 'Ваше імʼя']) ?>

        <?= $form->field($model, 'email')
            ->textInput(['placeholder' => 'Email']) ?>

        <?= $form->field($model, 'password')
            ->passwordInput(['placeholder' => 'Пароль']) ?>

        <div class="form-group">
            <?= Html::submitButton('Зареєструватися', [
                'class' => 'btn btn-primary'
            ]) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
