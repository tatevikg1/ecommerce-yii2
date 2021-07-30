<?php

/** @var  array $cartItems */

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;

?>


<div class="card">
    <div class="card-header">
        <h4>Your Cart Items</h4>
    </div>

    <div class="card-body p-0">
        <?php if(!empty($cartItems)): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <td>Product</td>
                    <td>Image</td>
                    <td>Unit price</td>
                    <td>Quantity</td>
                    <td>Total price</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $cartItem) : ?>
                    <tr>
                        <td><?= $cartItem['name'] ?></td>
                        <td>
                            <img src="<?= Product::displayImage($cartItem['image']) ?>" alt="<?= $cartItem['name'] ?>" style="width:50px">
                        </td>
                        <td><?= Yii::$app->formatter->asCurrency($cartItem['price']) ?></td>
                        <td><?= $cartItem['quantity'] ?></td>
                        <td><?= Yii::$app->formatter->asCurrency($cartItem['total_price']) ?></td>
                        <td><?= Html::a('Delete', ['/cart/delete', 'id' => $cartItem['id']], [
                                'class' => 'btn btn-outline-danger btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => 'Are you sure you dont want this product?'
                            ]) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <div class="card-body text-right">
        <a href="<?= Url::to(['/cart/checkout']) ?>"    
            class="btn btn-primary"
            data-method="post">
                Checkout
        </a>

        <?php else: ?>
            <p class="text-muted text-center p-5">
                There is no item in your cart
            </p>
        <?php endif; ?>

        </div>
    </div>
</div>