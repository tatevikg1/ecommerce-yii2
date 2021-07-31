<?php 

namespace frontend\base;

use common\models\CartItem;
use yii\base\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action)
    {

        if(isGuest()){
            $this->view->params['cartItemParam'] = CartItem::getTotalQuantityForGuest();
        }else{
            $this->view->params['cartItemParam'] = CartItem::getTotalQuantityForUser(auth()->id);
        }

        return parent::beforeAction($action);
    }
}