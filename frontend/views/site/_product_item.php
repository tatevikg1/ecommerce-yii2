<?php

/** @var $model \common\models\Product */

use yii\helpers\StringHelper;
use yii\helpers\Url;

?>

<div class="card h-100">
    <a href="">
        <img class="card-img-top" src="<?= $model->getImageUrl() ?>" alt="..." />
    </a>

    <div class="card-body">
        <div class="card-title">
            <a href="">
                <h5 class="fw-bolder"><?= $model->name ?></h5>
            </a>

            <h5><?= Yii::$app->formatter->asCurrency($model->price) ?></h5>

            <div class="card-text">
                <?= StringHelper::truncate(strip_tags($model->description), 30) ?>
            </div>
        </div>
    </div>

    <div class="card-footer text-right">
        <a class="btn btn-primary btn-add-to-cart" href="<?= Url::to(['/cart/add']) ?>">
            Add to Cart
         </a>
    </div>
</div>