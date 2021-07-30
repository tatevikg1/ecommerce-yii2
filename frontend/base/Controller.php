<?php 

namespace frontend\base;

use common\models\CartItem;
use Yii;
use yii\base\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action)
    {
        // $cartItemCount = CartItem::find()->userId(Yii::$app->user->id)->count();
        $cartItemCount = CartItem::findBySql(
            " SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId ",
            ['userId' => Yii::$app->user->id]
        )->scalar();

        $this->view->params['cartItemParam'] = $cartItemCount;
        
        return parent::beforeAction($action);
    }
}