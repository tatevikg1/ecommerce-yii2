<?php

namespace frontend\controllers;

use Yii;
use \frontend\base\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Profile controller
 */
class ProfileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'update-account', 'update-address'],
                'rules' => [
                    [
                        'actions' => ['index', 'update-account', 'update-address'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }


    public function actionIndex()
    {
        /** @var \common\models\user $user */
        $user = Yii::$app->user->identity;

        $userAddress = $user->getAddress();

        return $this->render('index', [
            'user' => $user,
            'userAddress' => $userAddress
        ]);
    }

    public function actionUpdateAddress()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Why you are here? It is for Ajax requests.');
        }

        /** @var \common\models\user $user */
        $user = Yii::$app->user->identity;
        $userAddress = $user->getAddress();
        $success = false;

        if ($userAddress->load(Yii::$app->request->post()) && $userAddress->save()) {
            $success = true;
        }

        return $this->renderPartial('_user_address', [
            'userAddress' => $userAddress,
            'success' => $success
        ]);
    }

    public function actionUpdateAccount()
    {
        /** @var \common\models\user $user */
        $user = Yii::$app->user->identity;
        $success = false;

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            $success = true;
        }

        return $this->renderPartial('_user_account', [
            'user' => $user,
            'success' => $success
        ]);
    }
}
