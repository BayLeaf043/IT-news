<?php

namespace tests\functional;

use app\models\User;
use FunctionalTester;
use Yii;

class LoginFormCest
{
    private string $route = 'site/login';

    public function _before(FunctionalTester $I)
    {
        $username = 'testlogin';

        $user = User::find()->where(['username' => $username])->one();

        if ($user === null) {
            $user = new User();
            $user->username = $username;
            $user->email = 'test_' . time() . '@mail.com'; 
            $user->setPassword('testpass');                 
            $user->generateAuthKey();
            $user->created_at = time();
            $user->is_admin = 0;
            $user->save(false);
        }
    }

    public function ensureLoginPageOpens(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);
        $I->see('Log In', 'h2');
        $I->seeElement('form#login-form');
    }

    public function loginWithEmptyFields(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);
        $I->click('Log in');
        $I->see('This field is required');
    }

    public function loginWithWrongPassword(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $I->fillField('LoginForm[username]', 'testlogin');
        $I->fillField('LoginForm[password]', 'wrong');
        $I->click('Log in');

        $I->see('Invalid login or password');
    }

    public function loginSuccessfully(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $I->fillField('LoginForm[username]', 'testlogin');
        $I->fillField('LoginForm[password]', 'testpass');
        $I->click('Log in');

        $I->see('testlogin');
       
    }
}