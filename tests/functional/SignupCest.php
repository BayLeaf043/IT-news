<?php

namespace tests\functional;

use FunctionalTester;
use app\models\User;

class SignupCest
{
    private string $route = 'site/signup'; 

    public function ensureSignupPageOpens(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);
        $I->see('Sign Up', 'h2');
        $I->seeElement('form#signup-form');
        $I->see('Sign up'); 
    }

    public function validateRequiredFields(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);
        $I->click('Sign up');
        $I->see('This field is required');
    }

    public function validateUsernamePattern(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $I->fillField('SignupForm[username]', 'логін_укр');
        $I->fillField('SignupForm[email]', 'test' . time() . '@mail.com');
        $I->fillField('SignupForm[password]', '12345');

        $I->click('Sign up');

        $I->see('Login: Latin letters, numbers and _');
    }

    public function validateEmailFormat(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $I->fillField('SignupForm[username]', 'valid_user_' . time());
        $I->fillField('SignupForm[email]', 'not_an_email');
        $I->fillField('SignupForm[password]', '12345');

        $I->click('Sign up');

        $I->see('Email is not a valid email address.');
    }

    public function validatePasswordMinLength(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $I->fillField('SignupForm[username]', 'valid_user_' . time());
        $I->fillField('SignupForm[email]', 'test' . time() . '@mail.com');
        $I->fillField('SignupForm[password]', '12');

        $I->click('Sign up');

        $I->see('Password must contain at least 4 characters');
    }

    public function validateUsernameUnique(FunctionalTester $I)
    {
        $user = new User();
        $user->username = 'existing_user';
        $user->email = 'existing_' . time() . '@mail.com';
        $user->setPassword('qwerty');
        $user->generateAuthKey();
        $user->created_at = time();
        $user->is_admin = 0;
        $user->save(false);

        $I->amOnRoute($this->route);

        $I->fillField('SignupForm[username]', 'existing_user');
        $I->fillField('SignupForm[email]', 'new_' . time() . '@mail.com');
        $I->fillField('SignupForm[password]', 'qwerty');

        $I->click('Sign up');

        $I->see('This login is already in use');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->amOnRoute($this->route);

        $username = 'user_' . time();
        $email = 'user_' . time() . '@mail.com';

        $I->fillField('SignupForm[username]', $username);
        $I->fillField('SignupForm[email]', $email);
        $I->fillField('SignupForm[password]', 'qwerty');

        $I->click('Sign up');

        $I->seeRecord(User::class, ['username' => $username, 'email' => $email]);
    }
}