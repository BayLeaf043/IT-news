<?php

namespace app\controllers;

use app\models\Tag;
use app\models\TagSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends Controller
{
    /**
     * @inheritDoc
     */

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

    /**
     * Lists all Tag models.
     *
     * @return string
     */

    // Перегляд списку тегів в адмін-панелі
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tag model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Перегляд одного тегу в адмін-панелі
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    // Створення нового тегу в адмін-панелі
    public function actionCreate()
    {
        $model = new Tag();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Оновлення існуючого тегу в адмін-панелі
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Видалення тегу в адмін-панелі
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $linksCount = $model->getArticleTags()->count();

        if ($linksCount > 0) {
            // видаляємо всі зв’язки тег - статті
            \app\models\ArticleTag::deleteAll(['tag_id' => $model->id]);

            Yii::$app->session->setFlash(
                'warning',
                "The tag was linked to {$linksCount} article(s). All links were removed."
            );
        }

        $model->delete();

        Yii::$app->session->setFlash('success', 'Tag deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Допоміжний метод для пошуку тегу за ID
    protected function findModel($id)
    {
        if (($model = Tag::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
