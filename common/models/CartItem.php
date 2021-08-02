<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property Product $product
 */
class CartItem extends \yii\db\ActiveRecord
{
    const SESSION_KEY = 'cart_items';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart_items}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'quantity', 'created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CartItemQuery(get_called_class());
    }

    public static function getTotalQuantityForUser($userId)
    {

        $cartItemCount = CartItem::findBySql(
            " SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId ",
            ['userId' => $userId]
        )->scalar();

        return $cartItemCount;
    }

    public static function getTotalQuantityForGuest()
    {
        $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        $cartItemCount = 0;
        foreach ($cartItems as $cartItem) {
            $cartItemCount += $cartItem['quantity'];
        }

        return $cartItemCount;
    }

    public static function getCartItemsForUser($userId)
    {
        return CartItem::findBySql(
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
            ['userId' => $userId]
        )->asArray()->all();
    }

    public static function getTotalPriceForUser($userId)
    {
        $cartItemCount = CartItem::findBySql(
            " SELECT SUM(c.quantity * p.price) 
                FROM cart_items c
                LEFT JOIN products p on p.id = c.product_id
            WHERE c.created_by = :userId ",
            ['userId' => $userId]
        )->scalar();

        return $cartItemCount;
    }


    public static function getTotalPriceForGuest()
    {
        $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        $cartItemCount = 0;
        foreach ($cartItems as $cartItem) {
            $cartItemCount += ($cartItem['quantity'] * $cartItem['price']);
        }

        return $cartItemCount;
    }

}
