<?php 

namespace frontend\base;

use common\models\CartItem;
use Yii;
use yii\base\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action)
    {

        if(Yii::$app->user->isGuest){
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $cartItemCount = 0;
            foreach($cartItems as $cartItem){
                $cartItemCount += $cartItem['quantity'];
            }
        }else{
            $cartItemCount = CartItem::findBySql(
                " SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId ",
                ['userId' => Yii::$app->user->id]
            )->scalar();    
        }

        $this->view->params['cartItemParam'] = $cartItemCount;
        
        return parent::beforeAction($action);
    }
}