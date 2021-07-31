<?php

namespace frontend\controllers;

use common\models\CartItem;
use common\models\Product;
use Yii;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            // 'access' => [
            //     'class' => AccessControl::class,
            //     'only' => ['index', 'add'],
            //     'rules' => [
            //         [
            //             'actions' => ['index', 'add'],
            //             'allow' => true,
            //             'roles' => ['?'],
            //         ],
            //     ],
            // ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post', 'delete'],
                ],
            ],
            // [
            //     'class' => 'yii\filters\AjaxFilter',
            //     'only' => ['add']
            // ],
            [
                'class' => ContentNegotiator::class,
                'only' => ['add', 'delete', 'update'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ]

        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        } else {
            // $cartItems = CartItem::find()->userId(Yii::$app->user->id)->all();
            $cartItems = CartItem::findBySql(
                "SELECT 
                    c.product_id AS id, 
                    p.price * c.quantity AS total_price,
                    p.image, 
                    p.name, 
                    p.price, 
                    c.quantity
                FROM cart_items c
                    LEFT JOIN products p ON p.id = c.product_id
                WHERE c.created_by = :userId",
                ['userId' => Yii::$app->user->id]
            )->asArray()->all();
        }

        return $this->render('index', [
            'cartItems' => $cartItems
        ]);
    }

    public function actionAdd()
    {
        $id = Yii::$app->request->post('id');

        $product = Product::findOne(['id' => $id, 'status' => 1]);

        if (!$product) {
            throw new NotFoundHttpException('Product does not exist');
        }

        if (Yii::$app->user->isGuest) {

            $found = false;
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);

            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $found = true;
                    $item['quantity']++;
                    break;
                }
            }

            if (!$found) {
                $cartItem = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price
                ];
                $cartItems[] = $cartItem;
            }

            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            $userId = Yii::$app->user->id;

            // $cartItem = CartItem::find()->userId($userId)->productId($id)->one();
            $cartItem = CartItem::find()->where(['created_by' => $userId, 'product_id' => $id])->one();


            if ($cartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem  = new CartItem();
                $cartItem->product_id = $id;
                $cartItem->created_by = Yii::$app->user->id;
                $cartItem->quantity  = 1;
            }


            if ($cartItem->save()) {
                return [
                    'success' => true
                ];
            }

            return [
                'success' => false,
                'errors' => $cartItem->errors
            ];
        }
    }

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');

        $this->deleteCartItem($id);

        return $this->response->redirect('index');
    }

    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $quantity = Yii::$app->request->post('quantity');
        $product = Product::find()->where(['id' => $id, 'status' => 1])->one();

        if (!$product) {
            throw new NotFoundHttpException('product not found');
        }

        if($quantity < 1){
            return;
        }

        if (isGuest()) {

            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);

            foreach ($cartItems as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);

            return [
                'success' => true,
                'totalQuantity' => CartItem::getTotalQuantityForGuest()
            ];
        }

        $cartItem = CartItem::find()->where(['created_by' => auth()->id, 'product_id' => $id])->one();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();

            return [
                'success' => true,
                'totalQuantity' => CartItem::getTotalQuantityForUser(auth()->id)
            ];
        }
    }

    protected function deleteCartItem($id)
    {
        if (Yii::$app->user->isGuest) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);

            foreach ($cartItems as $key => $cartItem) {
                if ($cartItem['id'] == $id) {
                    array_splice($cartItems, $key, 1);
                    break;
                }
            }

            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            CartItem::deleteAll(['product_id' => $id, 'created_by' => Yii::$app->user->id]);
        }
    }
}
