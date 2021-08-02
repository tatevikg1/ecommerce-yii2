<?php

use yii\bootstrap4\ActiveForm;

/** @var  \common\models\Order $order*/
/** @var  \common\models\orderAddress $orderAddress*/
/** @var  array $cartItems*/
/** @var integer $productQuaantity */

?>

<?php $form = ActiveForm::begin(['action' => ['/strip']]) ?>


<div class="row mt-3">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>Address Information</h5>
            </div>
            <div class="card-body">
                <?= $form->field($orderAddress, 'address')->textInput(['autofocus' => true]) ?>
                <div class="row">
                    <div class="col">
                        <?= $form->field($orderAddress, 'city') ?>
                    </div>
                    <div class="col">
                        <?= $form->field($orderAddress, 'state') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-9">
                        <?= $form->field($orderAddress, 'country') ?>
                    </div>
                    <div class="col-3">
                        <?= $form->field($orderAddress, 'zipcode') ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <?= $form->field($order, 'firstname') ?>
                    </div>
                    <div class="col">
                        <?= $form->field($order, 'lastname') ?>

                    </div>
                </div>

                <?= $form->field($order, 'email') ?>

            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5>Order summery</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td>Number of products</td>
                        <td class="text-right">
                            <?= $productQuantity ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Total price</td>
                        <td class="text-right">
                            <?= Yii::$app->formatter->asCurrency($totalPrice)  ?>
                        </td>
                    </tr>

                </table>
                <a class="text-right mt-3">
                    <button class="btn btn-secondary">Checkout</button>
                </a>
            </div>
        </div>
    </div>
</div>