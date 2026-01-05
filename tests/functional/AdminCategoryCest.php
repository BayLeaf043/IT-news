<?php

namespace tests\functional;

use app\models\User;
use app\models\Category;
use app\models\Article;
use FunctionalTester;
use Yii;
use yii\helpers\Url;

class AdminCategoryCest
{
    private int $adminId;

    public function _before(FunctionalTester $I)
    {
        $admin = User::findOne(['username' => 'Admin']);
        if (!$admin) {
            $admin = new User();
            $admin->username   = 'Admin';
            $admin->email      = 'admin_' . time() . '@mail.com';
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

    public function ensureIndexPageWorks(FunctionalTester $I)
    {
        $I->amOnRoute('category/index');
        $I->seeResponseCodeIs(200);

    }

    public function createCategory(FunctionalTester $I)
    {
        $category = new Category();
        $category->title = 'Test Category ' . time();
        $category->save(false);

        $I->seeRecord(Category::class, ['id' => $category->id]);
    }

    public function updateCategory(FunctionalTester $I)
    {
        $category = new Category();
        $category->title = 'Old Title ' . time();
        $category->save(false);

        $id = $category->id;

        $category->title = 'New Title ' . time();
        $category->save(false);

        $I->seeRecord(Category::class, ['id' => $id, 'title' => $category->title]);
    }

    /** DELETE: видалення порожньої категорії (має пройти) */
    public function deleteEmptyCategory(FunctionalTester $I)
    {
        $category = new Category();
        $category->title = 'To Delete ' . time();
        $category->save(false);

        $id = $category->id;

        $I->seeRecord(Category::class, ['id' => $id]);

        $I->seeRecord(\app\models\Category::class, ['id' => $id]);

        $I->sendAjaxPostRequest('/index.php?r=category/delete&id=' . $id);

        $I->dontSeeRecord(\app\models\Category::class, ['id' => $id]);
    }

    /** DELETE: категорія з привʼязаною статтею НЕ має видалитись */
    public function deleteCategoryWithArticlesShouldFail(FunctionalTester $I)
    {
        $category = new Category();
        $category->title = 'Has Articles ' . time();
        $category->save(false);

        $article = new Article();
        $article->title       = 'Article for category ' . time();
        $article->content     = '...';
        $article->category_id = $category->id;
        $article->author_id   = $this->adminId;
        $article->status      = 1;
        $article->views       = 0;
        $article->created_at  = time();
        $article->save(false);

        $I->sendAjaxPostRequest('/index.php?r=category/delete&id=' . $category->id);
        $I->seeRecord(\app\models\Category::class, ['id' => $category->id]);
    }
}