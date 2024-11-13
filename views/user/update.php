<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Обновление';
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="user-update">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
