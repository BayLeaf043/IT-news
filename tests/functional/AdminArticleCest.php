<?php

namespace tests\functional;

use app\models\User;
use app\models\Article;
use FunctionalTester;
use Yii;

class AdminArticleCest
{
    private int $adminId;

    public function _before(FunctionalTester $I)
    {
        $admin = User::findOne(['username' => 'Admin']);
        if (!$admin) {
            $admin = new User();
            $admin->username   = 'Admin';
            $admin->email      = 'polinabrazhnyk@gmail.com';
            $admin->created_at = time();
            $admin->is_admin   = 1;

            $admin->setPassword('1234');
            $admin->generateAuthKey();

            $admin->save(false);
        } else {
            if ((int)$admin->is_admin !== 1) {
                $admin->is_admin = 1;
                $admin->save(false);
            }
        }

        $this->adminId = (int)$admin->id;

        Yii::$app->user->login($admin);
    }

    /** READ: перевірка сторінки списку статей в адмінці */
    public function ensureIndexPageWorks(FunctionalTester $I)
    {
        $I->amOnRoute('article/index');
        $I->seeResponseCodeIs(200);

        
        $I->see('Articles');           
        $I->see('Create Article');     
    }

    /** CREATE: створення статті */
    public function createArticle(FunctionalTester $I)
    {
        $article = new Article();
        $article->title       = 'Тестова стаття';
        $article->content     = 'Тестовий контент для перевірки.';
        $article->category_id = 1;          
        $article->author_id   = $this->adminId;
        $article->status      = 1;
        $article->views       = 0;
        $article->created_at  = time();

        $article->save(false);

        $I->seeRecord(Article::class, [
            'title' => 'Тестова стаття',
        ]);
    }

    /** UPDATE: оновлення статті */
    public function updateArticle(FunctionalTester $I)
    {
        $article = new Article();
        $article->title       = 'Стара назва';
        $article->content     = 'Старий текст';
        $article->category_id = 1;
        $article->author_id   = $this->adminId;
        $article->status      = 1;
        $article->views       = 0;
        $article->created_at  = time();
        $article->save(false);

        $id = $article->id;

        $article->title   = 'Оновлена назва';
        $article->content = 'Оновлений текст';
        $article->save(false);

        $I->seeRecord(Article::class, [
            'id'    => $id,
            'title' => 'Оновлена назва',
        ]);
    }

    /** DELETE: видалення через роут (POST) */
    public function deleteArticle(FunctionalTester $I)
    {
        $article = new Article();
        $article->title       = 'Стаття для видалення';
        $article->content     = 'Текст для видалення';
        $article->category_id = 1;
        $article->author_id   = $this->adminId;
        $article->status      = 1;
        $article->views       = 0;
        $article->created_at  = time();
        $article->save(false);

        $id = $article->id;

        $I->amOnRoute('article/index');
        $I->seeRecord(\app\models\Article::class, ['id' => $id]);

        $I->amOnRoute('article/delete', ['id' => $id]); 


        $I->sendAjaxPostRequest(\yii\helpers\Url::toRoute(['article/delete', 'id' => $id]));
        $I->sendAjaxPostRequest('/index.php?r=article/delete&id=' . $id);

        $I->dontSeeRecord(\app\models\Article::class, ['id' => $id]);
    }
}