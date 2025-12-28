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
use yii\web\ForbiddenHttpException;
use app\models\Tag;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */

    // обмежує logout тільки для авторизованих користувачів. comment-create також зроблений POST.
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
                    'comment-create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */

    // Службові дії (error/captcha), стандартні для Yii2 basic.
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

    // Відображення головної сторінки з переліком статей, фільтрація по категорії та пошук.
    public function actionIndex()
    {
        $categoryId = (int)Yii::$app->request->get('category_id');
        $q = trim((string)Yii::$app->request->get('q', ''));

        $query = Article::find()
            ->where(['status' => 1])
            ->with('category')
            ->orderBy(['created_at' => SORT_DESC]);

        // фільтр по категорії
        $categoryTitle = null;
        if ($categoryId) {
            $cat = \app\models\Category::findOne($categoryId);
            $categoryTitle = $cat?->title;
            if ($categoryTitle) {
                $query->andWhere(['category_id' => $categoryId]);
            }
        }

        // пошук по тексту + тегах
        if ($q !== '') {
            $query->joinWith('tags t'); 
            $query->andWhere([
                'or',
                ['like', 'article.title', $q],
                ['like', 'article.content', $q],
                 ['like', 't.title', ltrim($q, '#')],
            ]);
            $query->distinct(); 
        }

        $pagination = new Pagination([
            'pageSize' => \Yii::$app->user->isGuest ? 5 : 10,
            'totalCount' => $query->count(),
        ]);

        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $pageTitle = $categoryTitle ? "IT news - {$categoryTitle}" : "IT news";
        if ($q !== '') {
            $pageTitle .= " - search: {$q}";
        }

        return $this->render('index', [
            'articles' => $articles,
            'pagination' => $pagination,
            'isGuestLimited' => \Yii::$app->user->isGuest,
            'categoryTitle' => $categoryTitle,
            'categoryId' => $categoryId,
            'pageTitle' => $pageTitle,
            'q' => $q,
        ]);
    }


    // Відображення однієї статті з усіма деталями, лічильником переглядів, коментарями та пов'язаними статтями.
    public function actionView($id)
    {
        $model = Article::find()
            ->where(['id' => $id, 'status' => 1])
            ->with(['category', 'author', 'tags'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Article not found.');
        }

        $sessionKey = 'viewed_article_' . $model->id;
        if (!Yii::$app->session->has($sessionKey)) {
            $model->updateCounters(['views' => 1]);
            Yii::$app->session->set($sessionKey, true);
            $model->refresh(); 
        }

        $commentsCount = $model->getComments()->where(['status' => 1])->count();
        $commentForm = new \app\models\Comment();
        $commentForm->article_id = $model->id;

        $comments = \app\models\Comment::find()
            ->where(['article_id' => $model->id, 'status' => 1])
            ->with('user')
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        $relatedByTag = [];

        if (!empty($model->tags)) {
        $tagIds = array_map(fn($t) => $t->id, $model->tags);

        $relatedByTag = \app\models\Article::find()
            ->alias('a')
            ->distinct()
            ->innerJoin('article_tag at', 'at.article_id = a.id')
            ->where(['a.status' => 1])
            ->andWhere(['at.tag_id' => $tagIds])
            ->andWhere(['<>', 'a.id', $model->id])
            ->with('category')
            ->orderBy(['a.created_at' => SORT_DESC])
            ->limit(5)
            ->all();
        }

        return $this->render('view', [
            'model' => $model,
            'commentsCount' => $commentsCount,
            'commentForm' => $commentForm,
            'comments' => $comments,
            'relatedByTag' => $relatedByTag,
        ]);
    }



    /**
     * Login action.
     *
     * @return Response|string
     */

    // Вхід користувача
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

    // Реєстрація користувача
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

    // Вихід користувача
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    // Додавання коментаря до статті
    public function actionCommentCreate($id)
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('Login required.');
        }

        $article = Article::findOne(['id' => (int)$id, 'status' => 1]);
        if (!$article) {
            throw new NotFoundHttpException('Article not found.');
        }

        $comment = new \app\models\Comment();
        $comment->article_id = $article->id;
        $comment->user_id = Yii::$app->user->id;
        $comment->status = 1;

        // parent_id (може бути пусто)
        $comment->parent_id = Yii::$app->request->post('parent_id') ?: null;

        if ($comment->load(Yii::$app->request->post()) && $comment->save()) {
            return $this->redirect(['site/view', 'id' => $article->id, '#' => 'discussion']);
        }

        Yii::$app->session->setFlash('error', 'Comment was not saved.');
        return $this->redirect(['site/view', 'id' => $article->id, '#' => 'discussion']);
    }


    // Сторінка перегляду статей за тегом (клікабельні #hashtags)
    public function actionTag($tag)
    {
        $tag = trim((string)$tag);

        $tag = ltrim($tag, '#');

        if ($tag === '') {
            throw new NotFoundHttpException('Tag not found.');
        }

        $tagModel = Tag::findOne(['title' => mb_strtolower($tag)]);
        if (!$tagModel) {
            throw new NotFoundHttpException('Tag not found.');
        }

        $query = $tagModel->getArticles()
            ->alias('a')
            ->where(['a.status' => 1])
            ->with('category')
            ->orderBy(['a.created_at' => SORT_DESC]);

        $pagination = new Pagination([
            'pageSize' => Yii::$app->user->isGuest ? 5 : 10,
            'totalCount' => $query->count(),
        ]);

        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $pageTitle = "IT news - #{$tagModel->title}";

        return $this->render('tag', [
            'tagModel' => $tagModel,
            'articles' => $articles,
            'pagination' => $pagination,
            'pageTitle' => $pageTitle,
        ]);
    }
}