<?php

namespace app\models;

use Yii;

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
 * @property string $tocken
 *
 * @property Ticket[] $tickets
 */
class User extends \yii\db\ActiveRecord
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
            [['first_name', 'last_name', 'phone', 'document_number', 'login', 'password', 'tocken'], 'required'],
            [['first_name', 'last_name', 'phone'], 'string', 'max' => 50],
            [['document_number'], 'string', 'max' => 40],
            [['login', 'password'], 'string', 'max' => 60],
            [['tocken'], 'string', 'max' => 255],
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
            'tocken' => 'Tocken',
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
}
