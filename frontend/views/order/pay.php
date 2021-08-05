<?php

use yii\helpers\Url;

/** @var \common\models\Order $order */
/** @var \common\models\OrderAddress $orderAddress */
/** @var int $totalQuantity */

?>
<script src="https://www.paypal.com/sdk/js?client-id=AcKIgr-Qz59ILdLIrtDZpGetpWdFNJRhPsWYslFor-0hAlMMRf1rL0KPkbZi6JRy-vOPTUMFnjll2xZv"> </script>

<h3 class="mt-3 mb-3">Summary of Order #<?= $order->id ?> </h3>
<hr>
<div class="row">
    <div class="col">
        <table class="table table-sm">
            <tr>
                <th>Account Information</th>

            <tr>
                <td> Firstname </td>
                <td class="text-right"> <?= $order->firstname ?> </td>
            </tr>
            <tr>
                <td> Lastname </td>
                <td class="text-right"> <?= $order->lastname ?> </td>
            </tr>
            <tr>
                <td> Email </td>
                <td class="text-right"> <?= $order->email ?></td>
            </tr>
        </table>

        <table class="table table-sm">
            <tr>
                <th>Address Information</th>
            </tr>
            <tr>
                <td> Address </td>
                <td class="text-right"><?= $orderAddress->address ?></td>
            </tr>
            <tr>
                <td> City </td>
                <td class="text-right"><?= $orderAddress->city ?></td>
            </tr>
            <tr>
                <td> State </td>
                <td class="text-right"><?= $orderAddress->state ?></td>
            </tr>
            <tr>
                <td> Country </td>
                <td class="text-right"><?= $orderAddress->country ?></td>
            </tr>
            <tr>
                <td> Zipcode </td>
                <td class="text-right"> <?= $orderAddress->zipcode ? $orderAddress->zipcode : "---" ?></td>
            </tr>
        </table>
    </div>

    <div class="col">
        <table class="table table-sm">
            <tr>
                <th>Products</th>
                
            </tr>
            <tr>
                <th>Image</th>
                <th>Product name</th>
                <th>Product quantity</th>
                <th>Product total price</th>
            </tr>
            <?php foreach ($order->orderItems as $item) : ?>
                <tr>
                    <td><img src="<?= $item->product->getImageUrl() ?>" alt="" style="width: 50px;"></td>
                    <td><?= $item->product_name ?></td>
                    <td><?= $item->quantity ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($item->quantity * $item->unit_price) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>Total</th>
                <th></th>
                <th> <?= $totalQuantity ?></th>
                <th> <?= Yii::$app->formatter->asCurrency($order->total_price) ?> </th>
            </tr>
        </table>
        <div id="paypal-button-container"></div>

    </div>

</div>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {

            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: <?= $order->total_price ?>
                    }
                }]
            });
        },
        onApprove: function(data, actions) {

            // This function captures the funds from the transaction.
            return actions.order.capture().then(function(details) {

                const $form = $('#checkout-form');
                const formData= $form.serializeArray();
                formData.push({
                    name: 'transactionId',
                    value: details.id
                });
                formData.push({
                    name: 'status',
                    value: details.status
                });
                formData.push({
                    name: 'orderId',
                    value: data.orderId
                });

                $.ajax({
                    method: 'post',
                    url: ' <?= Url::toRoute(['/order/pay', 'orderId' => $order->id]) ?>',
                    data: data,
                    success: function(res) {
                        console.log(res);
                        // This function shows a transaction success message to your buyer.
                        alert('Transaction completed by ' + details.payer.name.given_name);
                        window.location.href = '';

                    }
                })
            });
        }
    }).render('#paypal-button-container');
</script>