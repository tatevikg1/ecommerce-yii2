<?php

namespace frontend\base;

use common\models\CartItem;
use yii\base\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action)
    {
        $this->view->params['cartItemParam'] = CartItem::getTotalQuantityForUser();

        return parent::beforeAction($action);
    }
}
