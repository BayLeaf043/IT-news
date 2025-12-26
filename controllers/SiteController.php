<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\Article;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Article::find()
            ->where(['status' => 1])
            ->with('category')
            ->orderBy(['created_at' => SORT_DESC]);

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->user->isGuest ? 5 : 10,
            'totalCount' => $query->count(),
        ]);

        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'articles' => $articles,
            'pagination' => $pagination,
            'isGuestLimited' => \Yii::$app->user->isGuest,
        ]);
    }


    public function actionView($id)
    {
        $model = Article::find()
            ->where(['id' => $id, 'status' => 1])
            ->with(['category', 'author', 'tags'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Article not found.');
        }

        // +1 view (захист від накрутки при F5)
        $sessionKey = 'viewed_article_' . $model->id;
        if (!Yii::$app->session->has($sessionKey)) {
            $model->updateCounters(['views' => 1]);
            Yii::$app->session->set($sessionKey, true);
            $model->refresh(); // щоб одразу показало оновлені views
        }

        // коментарі поки не виводимо, але число можемо порахувати
        $commentsCount = $model->getComments()->where(['status' => 1])->count();

        return $this->render('view', [
            'model' => $model,
            'commentsCount' => $commentsCount,
        ]);
    }



    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && ($user = $model->signup())) {
            Yii::$app->user->login($user);
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


}
