<?php

/** @var $this yii\web\View */

use yii\bootstrap4\LinkPager;
use yii\widgets\ListView;

/** @var $dataProvider \yii\data\ActiveDataProveder */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_product_item',
            'layout' => '{summary}<div class="row">{items}</div>{pager}',
            'itemOptions' => [
                'class' => 'col-lg-4 col-md-6 mb-4 product-item'
            ],
            'pager' => [ 
                'class' => LinkPager::class 
            ]
        ]) ?>
    </div>
</div>