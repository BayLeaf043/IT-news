<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    // таблиця в БД
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    // правила валідації
    public function rules(): array
    {
        return [
            [['username', 'email', 'password_hash', 'auth_key', 'created_at'], 'required'],
            [['created_at'], 'integer'],
            [['is_admin'], 'boolean'],
            [['username'], 'string', 'max' => 50],
            [['email', 'password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
        ];
    }

    // пошук користувача за ID
    public static function findIdentity($id): ?IdentityInterface
    {
        return static::findOne(['id' => $id]);
    }

    // Не використовується в цьому додатку
    public static function findIdentityByAccessToken($token, $type = null): ?IdentityInterface
    {
        return null; 
    }

    // отримання ID користувача
    public function getId(): int|string|null
    {
        return $this->id;
    }

    // отримання ключа автентифікації
    public function getAuthKey(): string
    {
        return (string)$this->auth_key;
    }

    // перевірка ключа автентифікації
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    // пошук користувача за логіном
    public static function findByUsername(string $username): ?self
    {
        return static::findOne(['username' => $username]);
    }

    // пошук користувача за email
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
    }

    // перевірка пароля
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // встановлення пароля
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    // генерація ключа автентифікації
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString(32);
    }

    // перевірка чи є користувач адміністратором
    public function isAdmin(): bool
    {
        return (bool)$this->is_admin;
    }
}
