<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddress;
use \frontend\base\Controller;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Order controller
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'checkout' => ['get','post'],
                ],
            ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['create'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ]

        ];
    }


    public function actionCheckout()
    {
        $cartItems = CartItem::getCartItemsForUser();
        $productQuantity = CartItem::getTotalQuantityForUser();
        $totalPrice = CartItem::getTotalPriceForUser();

        if (empty($cartItems))  return $this->response->redirect('/site/index');

        $order = new Order();
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        $order->created_by = auth()->id;
        $order->total_price = CartItem::getTotalPriceForUser();

        $orderAddress = new OrderAddress();

        $transaction = Yii::$app->db->beginTransaction();

        if ($order->load(Yii::$app->request->post()) && $order->save() && $order->saveOrderItems()) {

            $orderAddress->order_id = $order->id;

            if ($orderAddress->load(Yii::$app->request->post()) && $orderAddress->save()) {

                $transaction->commit();

                CartItem::clearCartItemsForUser();

                return $this->render('pay', [
                    'order' => $order,
                    'orderAddress' => $order->orderAddress,
                    'totalQuantity' => $productQuantity
                ]);
            } else {
                $transaction->rollBack();

                return [
                    'success' => false,
                    'errors' => $orderAddress->errors
                ];
            }
        }
        // $transaction->rollBack();

        if (auth()) {
            $user = auth();

            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;

            $userAddress = $user->getAddress();

            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;
        }

        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice
        ]);
    }

    public function actionPay()
    {
        $orderId = Yii::$app->request->get('orderId');
        if(auth()){
            $order = Order::findOne([
                'id' => $orderId, 
                'status' => Order::STATUS_DRAFT,
                'created_by' => auth()->id
            ]);
        }else{
            $order = Order::findOne(['id' => $orderId, 'status' => Order::STATUS_DRAFT]);
        }

        // TODO validate transactionId

        if(!$order){
            throw new NotFoundHttpException('the order with that id does not exest');
        }

        $order->transaction_id = Yii::$app->request->post('transactionId');
        $status = Yii::$app->request->post('status');
        $order->status = $status === 'COMPLETED' ? Order::STATUS_COMPLETED : Order::STATUS_FAILED;
        $order->save();
    }


}
