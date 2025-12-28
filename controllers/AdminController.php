<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends Controller
{
    // обмежує доступ лише для авторизованих користувачів, які мають роль адміністратора (is_admin = 1)

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest
                                && Yii::$app->user->identity->is_admin == 1;
                        },
                    ],
                ],
                // Повідомлення у випадку спроби доступу без прав адміністратора

                'denyCallback' => function () {
                    throw new ForbiddenHttpException('You are not allowed to access this page.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // головна сторінка адміністративної панелі (керування статтями, категоріями, тегами та коментарями)
    public function actionIndex()
    {
        return $this->render('index');
    }
}