<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id_user
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $document_number
 * @property string $login
 * @property string $password
 * @property string $token
 *
 * @property Ticket[] $tickets
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'document_number', 'login', 'password'], 'required'],
            [['first_name', 'last_name', 'phone'], 'string', 'max' => 50],
            [['document_number'], 'string', 'max' => 40],
            [['login', 'password'], 'string', 'max' => 60],
            [['token'], 'string', 'max' => 255],
            [['login'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'document_number' => 'Document Number',
            'login' => 'Login',
            'password' => 'Password',
            'token' => 'Token',
        ];
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['user_id' => 'id_user']);
    }

    public static function findIdentity($id_user)
    {
        return static::findOne($id_user);
    }
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getAuthKey()
    {
        return ;
    }

    public function validateAuthKey($authKey)
    {
        return ;
    }
    public function validatePassword($password)

    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        if (Yii::$app->getSecurity()->validatePassword($password, $hash)) {
            return $this;
        } else {
            return 0;
        }
    }

    public function fields()
    {
        $fields = parent::fields();
// удаляем небезопасные поля
        unset($fields['password'],$fields['id_user'], $fields['token']);
        return $fields;
    }
}
