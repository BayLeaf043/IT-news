<?php

namespace app\controllers;

use app\models\Article;
use app\models\ArticleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
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
     * Lists all Article models.
     *
     * @return string
     */

    // Відображення списку всіх статей у адмін-панелі
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Перегляд однієї статті в адмін-панелі
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */

    // Створення нової статті в адмін-панелі
    public function actionCreate()
    {
        $model = new Article();

        // автоматично прив’язуємо автора
        $model->author_id = \Yii::$app->user->id;

        if ($this->request->isPost && $model->load($this->request->post())) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->imageFile) {
                $uploadDir = \Yii::getAlias('@webroot/uploads');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid('img_') . '.' . $model->imageFile->extension;
                $filePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

                if ($model->imageFile->saveAs($filePath)) {
                    $model->image = 'uploads/' . $fileName; // шлях піде в БД
                }
            }

            if ($model->save()) {

                // збереження хештегів та зв’язків article_tag
                $model->saveTagsFromInput();
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Оновлення існуючої статті
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImage = $model->image;

        if ($this->request->isPost && $model->load($this->request->post())) {

            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->imageFile) {
                $uploadDir = \Yii::getAlias('@webroot/uploads');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = uniqid('img_') . '.' . $model->imageFile->extension;
                $filePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

                if ($model->imageFile->saveAs($filePath)) {
                    $model->image = 'uploads/' . $fileName;
                }
                } else {
                    $model->image = $oldImage;
                }

                if ($model->save()) {
                    $model->saveTagsFromInput();
                    return $this->redirect(['view', 'id' => $model->id]);
                }

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Видалення статті
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

    // Допоміжний метод для пошуку статті за ID
    protected function findModel($id)
    {
        if (($model = Article::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
