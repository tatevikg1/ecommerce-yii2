<?php

function isGuest()
{
    return Yii::$app->user->isGuest;
}

function auth()
{
    return Yii::$app->user;
}