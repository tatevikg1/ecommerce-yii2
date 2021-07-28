<?php

/** @var \yii\web\view $this */
/** @var \common\models\UserAddresss $userAddress */
/** @var bool $success */


use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

?>

<?php if (isset($success)) : ?>
    <?php if ($success == true) : ?>
        <div class="alert alert-success">
            Your address was successfully updated.
        </div>
    <?php endif ?>
    <?php if ($success == false) : ?>
        <div class="alert alert-danger">
            Your address was not updated.
        </div>
    <?php endif ?>
<?php endif ?>

<?php $addressForm = ActiveForm::begin([
    'action' => ['/profile/update-address'],
    'options' => [
        'data-pjax' => 1,
        'id' => 'address'
    ]
]) ?>

<?= $addressForm->field($userAddress, 'address') ?>
<div class="row">
    <div class="col">
        <?= $addressForm->field($userAddress, 'city') ?>
    </div>
    <div class="col">
        <?= $addressForm->field($userAddress, 'state') ?>
    </div>
</div>
<?= $addressForm->field($userAddress, 'country') ?>
<?= $addressForm->field($userAddress, 'zipcode') ?>
<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end() ?>
