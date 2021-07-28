<?php

/** @var \common\models\User $user */

use yii\widgets\Pjax;

/** @var \yii\web\view $this */
/** @var \common\models\UserAddresss $userAddress */

?>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                Address Information
            </div>
            <div class="card-body">
                <?php Pjax::begin(['enablePushState' => false]) ?>

                <?= $this->render('_user_address', [
                    'userAddress' => $userAddress
                ]) ?>

                <?php Pjax::end() ?>
            </div>
        </div>

    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                Account Information
            </div>
            <div class="card-body">
                <?php Pjax::begin(['enablePushState' => false]) ?>

                <?= $this->render('_user_account', [
                    'user' => $user
                ]) ?>
                <?php Pjax::end() ?>

            </div>
        </div>

    </div>
</div>