<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private ?User $_user = null;

    // правила валідації
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required', 'message' => 'This field is required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    // підписи атрибутів
    public function attributeLabels(): array
    {
        return [
            'username' => 'Login',
            'password' => 'Password',
            'rememberMe' => 'Remember me',
        ];
    }

    // перевірка пароля
    public function validatePassword($attribute, $params): void
    {
        if ($this->hasErrors()) return;

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Invalid login or password');
        }
    }

    // логін користувача
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0
            );
        }
        return false;
    }

    // отримання користувача за логіном
    private function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername((string)$this->username);
        }
        return $this->_user;
    }
}
