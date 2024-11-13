<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\Model;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    public $password;


    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['username', 'password_hash', 'email'], 'required'],
            [['username', 'email'], 'unique'],
            [['auth_key', 'access_token'], 'string', 'max' => 32],
            [['username', 'email', 'password_hash'], 'string', 'max' => 255],
            [['created_at'], 'safe']
        ];
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert || !$this->access_token) {
                $this->generateAccessToken();
            }
            return true;
        }
        return false;
    }
}
