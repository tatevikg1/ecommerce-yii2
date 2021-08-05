<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Order;
use common\models\OrderAddress;
use \frontend\base\Controller;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
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
                    'checkout' => ['get', 'post'],
                ],
            ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['create', 'pay'],
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
            /** @var \common\models\User $user  */
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
        // check if there is an order with the given id in the database (there should be)
        $orderId = Yii::$app->request->get('orderId');
        if (auth()) {
            $order = Order::findOne(['id' => $orderId, 'status' => Order::STATUS_DRAFT, 'created_by' => auth()->id]);
        } else {
            $order = Order::findOne(['id' => $orderId, 'status' => Order::STATUS_DRAFT]);
        }
        if (!$order)   throw new NotFoundHttpException('the order with that id does not exest');

        // chack if there already is an order with the given paypal order id in the database (there should not be)
        $paypalOrderId = Yii::$app->request->post('orderID');
        $orderAlreadyExists = Order::find()->andWhere(['paypal_order_id' => $paypalOrderId])->exists();
        if ($orderAlreadyExists) {
            throw new BadRequestHttpException();
        }

        // validate transaction on paypal
        $environment = new SandboxEnvironment(Yii::$app->params['paypalClientId'], Yii::$app->params['paypalClientSecret']);
        $client = new PayPalHttpClient($environment);

        /** @var PayPalCheckoutSdk\Orders\OrdersGetRequest $response */
        $response = $client->execute(new OrdersGetRequest($paypalOrderId));

        if ($response->statusCode === 200) {
            $order->paypal_order_id = $paypalOrderId;
            $status = $response->result->status;
            $order->status = $status === 'COMPLETED' ? Order::STATUS_COMPLETED : Order::STATUS_FAILED;

            $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;

            // check it the paypal paid amount and currnecy is the same as in the database (it should not be changed)
            $paidAmount = 0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                if ($purchase_unit->amount->currency_code == 'USD') {
                    $paidAmount += $purchase_unit->amount->value;
                }
            }
            if ($order->save()) {
                return ['success' => true];
            }
        }

        throw new BadRequestHttpException();
    }
}
