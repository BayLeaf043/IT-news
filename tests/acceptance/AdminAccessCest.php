<?php

class AdminAccessCest
{
    public function guestCannotAccessAdminPanel(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/index');

        // Гість бачить публічну частину сайту
        $I->see('IT News Portal');

        // Але не бачить елементів керування
        $I->dontSee('Create');
        $I->dontSee('Update');
        $I->dontSee('Delete');
        $I->dontSee('Admin panel');
    }
}
