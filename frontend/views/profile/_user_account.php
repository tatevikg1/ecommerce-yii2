<?php

/** @var \yii\web\view $this */
/** @var \common\models\User $user */
/** @var bool $success */


use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;



if (isset($success)) : ?>
    <?php if ($success == true) : ?>
        <div class="alert alert-success">
            Your account was successfully updated.
        </div>
    <?php endif ?>
    <?php if ($success == false) : ?>
        <div class="alert alert-danger">
            Your account was not updated.
        </div>
    <?php endif ?>
<?php endif ?>

<?php $form = ActiveForm::begin([
    'action' => ['/profile/update-account'],
    'options' => [
        'data-pjax' => 1,
        'id' => 'account'
    ]
]) ?>

<?= $form->field($user, 'username')->textInput(['autofocus' => true]) ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($user, 'firstname')->textInput(['autofocus' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($user, 'lastname')->textInput(['autofocus' => true]) ?>
    </div>
</div>
<?= $form->field($user, 'email') ?>

<div class="row">
    <div class="col">
        <?= $form->field($user, 'password')->passwordInput() ?>

    </div>
    <div class="col">
        <?= $form->field($user, 'passwordConfirm')->passwordInput() ?>

    </div>
</div>
<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
</div>

<?php ActiveForm::end(); ?>
