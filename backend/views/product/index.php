<?php

use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'id',
                'contentOptions' => [
                    'style' => 'width:55px'
                ]
            ],
            'name',
            [
                'attribute' => 'image',
                'label' =>  'Image',
                'contentOptions' => [
                    'style' => 'width:65px'
                ],
                'content' => function($model){
                    /**
                     * @var \common\models\Product $model
                    */
                    return Html::img($model->getImageUrl(), ['style' => 'width: 50px']);
                }
            ],
            [
                'attribute' => 'price',
                'contentOptions' => [
                    'style' => 'width:150px',
                ],
                'format' => 'currency'
            ],

            [
                'attribute' => 'status',
                'contentOptions' => [
                    'style' => 'width:50px'
                ],
                'content' => function($model){
                    /**
                     * @var \common\models\Product $model
                    */
                    return Html::tag('span', $model->status ? 'Active' : 'Draft', [
                        'class' => $model->status ? 'badge badge-success' : 'badge badge-danger'
                    ]);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => [
                    'style' => 'white-space:nowrap'
                ]
            ],
            // 'created_at:datetime',
            'updated_at:datetime',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [ 'class' => LinkPager::class ]

    ]); ?>


</div>
