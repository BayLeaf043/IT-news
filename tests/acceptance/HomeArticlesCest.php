<?php

class HomeArticlesCest
{
    public function articlesListIsDisplayed(AcceptanceTester $I)
    {
        $I->amOnPage('/');

        // заголовок сторінки
        $I->see('IT News', 'h1');

        // є картки
        $I->seeElement('.article-card');
        $I->seeElement('.article-title');
        $I->seeElement('.article-card-link');

        // у картці є текст превʼю (з твого шаблону .article-preview)
        $I->seeElement('.article-preview');

        // клік на першу статтю і перевірка сторінки статті
        $I->click('.article-card-link');
        $I->seeElement('.article-page');
        $I->seeElement('.article-content');
    }
}
