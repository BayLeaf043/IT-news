<?php

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'required', 'message' => 'This field is required'],
            ['username', 'string', 'max' => 50],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Login: Latin letters, numbers and _'],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username', 'message' => 'This login is already in use'],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email', 'message' => 'This email is already in use'],
            ['password', 'string', 'min' => 4, 'tooShort' => 'Password must contain at least 4 characters'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Login',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function signup(): ?User
    {
        if (!$this->validate()) return null;

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->created_at = time();
        $user->is_admin = 0;

        return $user->save() ? $user : null;
    }

}